<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$fazendaId = require_active_fazenda();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: animais_listar.php');
    exit;
}

verify_csrf_or_abort();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$numeroBrinco = trim((string) ($_POST['numero_brinco'] ?? ''));
$raca = trim((string) ($_POST['raca'] ?? ''));
$categoriaAtual = trim((string) ($_POST['categoria_atual'] ?? ''));
$statusReprodutivo = trim((string) ($_POST['status_reprodutivo'] ?? ''));
$loteId = filter_input(INPUT_POST, 'lote_id', FILTER_VALIDATE_INT);
$etapaAtual = trim((string) ($_POST['etapa_atual'] ?? ''));
$retiroAtual = trim((string) ($_POST['retiro_atual'] ?? ''));
$dataNasc = (string) ($_POST['data_nasc'] ?? '');
$sexo = (string) ($_POST['sexo'] ?? '');
$maeId = filter_input(INPUT_POST, 'mae_id', FILTER_VALIDATE_INT);

if (!$id || $numeroBrinco === '') {
    redirect_with_message('animais_listar.php', 'Dados inválidos.', 'danger');
}

$stmtAnimal = db()->prepare('SELECT id FROM animais WHERE id = :id AND fazenda_id = :fazenda_id');
$stmtAnimal->execute([':id' => $id, ':fazenda_id' => $fazendaId]);
if (!$stmtAnimal->fetchColumn()) {
    redirect_with_message('animais_listar.php', 'Animal não encontrado na fazenda ativa.', 'danger');
}

if ($loteId) {
    $stmtLote = db()->prepare('SELECT 1 FROM lotes WHERE id = :id AND fazenda_id = :fazenda_id');
    $stmtLote->execute([':id' => $loteId, ':fazenda_id' => $fazendaId]);
    if (!$stmtLote->fetchColumn()) {
        redirect_with_message('animais_editar.php?id=' . $id, 'Lote inválido para esta fazenda.', 'danger');
    }
}

if ($maeId) {
    $stmtMae = db()->prepare('SELECT 1 FROM animais WHERE id = :id AND fazenda_id = :fazenda_id AND id <> :animal_id');
    $stmtMae->execute([':id' => $maeId, ':fazenda_id' => $fazendaId, ':animal_id' => $id]);
    if (!$stmtMae->fetchColumn()) {
        redirect_with_message('animais_editar.php?id=' . $id, 'Mãe inválida para esta fazenda.', 'danger');
    }
}

$sql = 'UPDATE animais
        SET numero_brinco = :numero_brinco,
            raca = :raca,
            categoria_atual = :categoria_atual,
            status_reprodutivo = :status_reprodutivo,
            lote_id = :lote_id,
            etapa_atual = :etapa_atual,
            retiro_atual = :retiro_atual,
            data_nasc = :data_nasc,
            sexo = :sexo,
            mae_id = :mae_id,
            updated_at = NOW()
        WHERE id = :id AND fazenda_id = :fazenda_id';

try {
    $stmt = db()->prepare($sql);
    $stmt->execute([
        ':numero_brinco' => $numeroBrinco,
        ':raca' => $raca ?: null,
        ':categoria_atual' => $categoriaAtual ?: null,
        ':status_reprodutivo' => $statusReprodutivo ?: null,
        ':lote_id' => $loteId ?: null,
        ':etapa_atual' => $etapaAtual ?: null,
        ':retiro_atual' => $retiroAtual ?: null,
        ':data_nasc' => $dataNasc !== '' ? $dataNasc : null,
        ':sexo' => in_array($sexo, ['M', 'F'], true) ? $sexo : null,
        ':mae_id' => $maeId ?: null,
        ':id' => $id,
        ':fazenda_id' => $fazendaId,
    ]);
} catch (PDOException $e) {
    if ((string) $e->getCode() === '23505') {
        redirect_with_message('animais_editar.php?id=' . $id, 'Número de brinco já cadastrado nesta fazenda.', 'danger');
    }
    redirect_with_message('animais_editar.php?id=' . $id, 'Erro ao atualizar animal.', 'danger');
}

redirect_with_message('animais_listar.php', 'Animal atualizado com sucesso.');
