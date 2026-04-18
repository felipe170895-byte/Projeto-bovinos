<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function current_user(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function require_auth(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function is_admin(): bool
{
    $user = current_user();
    return $user !== null && $user['perfil'] === 'admin';
}

function require_admin(): void
{
    require_auth();

    if (!is_admin()) {
        http_response_code(403);
        exit('Acesso negado.');
    }
}

function current_fazenda_id(): ?int
{
    $id = $_SESSION['fazenda_id'] ?? null;
    return is_numeric($id) ? (int) $id : null;
}

function user_fazendas(int $usuarioId): array
{
    $sql = 'SELECT f.id, f.nome
            FROM usuarios_fazendas uf
            INNER JOIN fazendas f ON f.id = uf.fazenda_id
            WHERE uf.usuario_id = :usuario_id AND f.ativa = TRUE
            ORDER BY f.nome';
    $stmt = db()->prepare($sql);
    $stmt->execute([':usuario_id' => $usuarioId]);

    return $stmt->fetchAll();
}

function user_has_fazenda_access(int $usuarioId, int $fazendaId): bool
{
    if (is_admin()) {
        return true;
    }

    $stmt = db()->prepare('SELECT 1 FROM usuarios_fazendas uf INNER JOIN fazendas f ON f.id = uf.fazenda_id WHERE uf.usuario_id = :usuario_id AND uf.fazenda_id = :fazenda_id AND f.ativa = TRUE');
    $stmt->execute([
        ':usuario_id' => $usuarioId,
        ':fazenda_id' => $fazendaId,
    ]);

    return (bool) $stmt->fetchColumn();
}

function ensure_active_fazenda(): void
{
    $user = current_user();
    if ($user === null) {
        return;
    }

    $fazendas = user_fazendas((int) $user['id']);
    if (!$fazendas) {
        unset($_SESSION['fazenda_id']);
        return;
    }

    $allowedIds = array_column($fazendas, 'id');
    $currentId = current_fazenda_id();

    if ($currentId === null || !in_array($currentId, array_map('intval', $allowedIds), true)) {
        $_SESSION['fazenda_id'] = (int) $fazendas[0]['id'];
    }
}

function require_active_fazenda(): int
{
    require_auth();
    ensure_active_fazenda();

    $fazendaId = current_fazenda_id();
    if ($fazendaId === null) {
        redirect_with_message('fazendas_listar.php', 'Selecione ou vincule uma fazenda para continuar.', 'warning');
    }

    return $fazendaId;
}

function redirect_with_message(string $location, string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
    header('Location: ' . $location);
    exit;
}

function flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}
