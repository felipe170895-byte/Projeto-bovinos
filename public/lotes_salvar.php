<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$fazendaId = require_active_fazenda();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: lotes_listar.php');
    exit;
}

verify_csrf_or_abort();

$nome = trim((string) ($_POST['nome'] ?? ''));
$etapa = trim((string) ($_POST['etapa'] ?? ''));
$retiro = trim((string) ($_POST['retiro'] ?? ''));
$descricao = trim((string) ($_POST['descricao'] ?? ''));

if ($nome === '') {
    redirect_with_message('lotes_novo.php', 'Nome do lote é obrigatório.', 'danger');
}

try {
    $stmt = db()->prepare('INSERT INTO lotes (fazenda_id, nome, etapa, retiro, descricao) VALUES (:fazenda_id, :nome, :etapa, :retiro, :descricao)');
    $stmt->execute([
        ':fazenda_id' => $fazendaId,
        ':nome' => $nome,
        ':etapa' => $etapa ?: null,
        ':retiro' => $retiro ?: null,
        ':descricao' => $descricao ?: null,
    ]);
} catch (PDOException $e) {
    if ((string) $e->getCode() === '23505') {
        redirect_with_message('lotes_novo.php', 'Já existe um lote com este nome nesta fazenda.', 'danger');
    }
    redirect_with_message('lotes_novo.php', 'Erro ao salvar lote.', 'danger');
}

redirect_with_message('lotes_listar.php', 'Lote cadastrado com sucesso.');
