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

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['csrf_token'];
}

function csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf_or_abort(): void
{
    $token = (string) ($_POST['csrf_token'] ?? '');
    $sessionToken = (string) ($_SESSION['csrf_token'] ?? '');

    if ($token === '' || $sessionToken === '' || !hash_equals($sessionToken, $token)) {
        http_response_code(419);
        exit('Sessão expirada ou token CSRF inválido. Recarregue a página e tente novamente.');
    }
}

function apply_security_headers(): void
{
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' data:; font-src 'self' https://cdn.jsdelivr.net; connect-src 'self'; frame-ancestors 'self';");
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

function log_audit(string $acao, ?int $fazendaId = null, ?string $detalhes = null): void
{
    $user = current_user();
    $usuarioId = $user['id'] ?? null;

    try {
        $stmt = db()->prepare('INSERT INTO audit_logs (usuario_id, fazenda_id, acao, detalhes, ip_origem, user_agent) VALUES (:usuario_id, :fazenda_id, :acao, :detalhes, :ip_origem, :user_agent)');
        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':fazenda_id' => $fazendaId,
            ':acao' => $acao,
            ':detalhes' => $detalhes,
            ':ip_origem' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':user_agent' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
        ]);
    } catch (Throwable $e) {
        // Não interromper fluxo de negócio por falha de auditoria.
    }
}

function is_login_rate_limited(string $email): bool
{
    $stmt = db()->prepare("SELECT COUNT(*) FROM login_attempts WHERE email = :email AND attempted_at >= (NOW() - INTERVAL '15 minutes')");
    $stmt->execute([':email' => strtolower(trim($email))]);
    return (int) $stmt->fetchColumn() >= 8;
}

function register_login_attempt(string $email, bool $success): void
{
    $stmt = db()->prepare('INSERT INTO login_attempts (email, success, ip_origem, attempted_at) VALUES (:email, :success, :ip_origem, NOW())');
    $stmt->execute([
        ':email' => strtolower(trim($email)),
        ':success' => $success,
        ':ip_origem' => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);
}

function clear_login_attempts(string $email): void
{
    $stmt = db()->prepare('DELETE FROM login_attempts WHERE email = :email');
    $stmt->execute([':email' => strtolower(trim($email))]);
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
