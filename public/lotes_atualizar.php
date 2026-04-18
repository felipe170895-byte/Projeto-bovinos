<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$fazendaId = require_active_fazenda();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: lotes_listar.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nome = trim((string) ($_POST['nome'] ?? ''));
$etapa = trim((string) ($_POST['etapa'] ?? ''));
$retiro = trim((string) ($_POST['retiro'] ?? ''));
$descricao = trim((string) ($_POST['descricao'] ?? ''));
$ativo = isset($_POST['ativo']) && (int) $_POST['ativo'] === 1;

if (!$id || $nome === '') {
    redirect_with_message('lotes_listar.php', 'Dados inválidos.', 'danger');
}

$stmtCheck = db()->prepare('SELECT 1 FROM lotes WHERE id = :id AND fazenda_id = :fazenda_id');
$stmtCheck->execute([':id' => $id, ':fazenda_id' => $fazendaId]);
if (!$stmtCheck->fetchColumn()) {
    redirect_with_message('lotes_listar.php', 'Lote não encontrado na fazenda ativa.', 'danger');
}

try {
    $stmt = db()->prepare('UPDATE lotes SET nome = :nome, etapa = :etapa, retiro = :retiro, descricao = :descricao, ativo = :ativo, updated_at = NOW() WHERE id = :id AND fazenda_id = :fazenda_id');
    $stmt->execute([
        ':id' => $id,
        ':fazenda_id' => $fazendaId,
        ':nome' => $nome,
        ':etapa' => $etapa ?: null,
        ':retiro' => $retiro ?: null,
        ':descricao' => $descricao ?: null,
        ':ativo' => $ativo,
    ]);
} catch (PDOException $e) {
    if ((string) $e->getCode() === '23505') {
        redirect_with_message('lotes_editar.php?id=' . $id, 'Já existe um lote com este nome na fazenda.', 'danger');
    }
    redirect_with_message('lotes_editar.php?id=' . $id, 'Erro ao atualizar lote.', 'danger');
}

redirect_with_message('lotes_listar.php', 'Lote atualizado com sucesso.');
