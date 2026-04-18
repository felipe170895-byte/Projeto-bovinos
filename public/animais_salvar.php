<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$fazendaId = require_active_fazenda();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: animais_listar.php');
    exit;
}

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

if ($numeroBrinco === '') {
    redirect_with_message('animais_novo.php', 'Número do brinco é obrigatório.', 'danger');
}

if ($loteId) {
    $stmtLote = db()->prepare('SELECT 1 FROM lotes WHERE id = :id AND fazenda_id = :fazenda_id');
    $stmtLote->execute([':id' => $loteId, ':fazenda_id' => $fazendaId]);
    if (!$stmtLote->fetchColumn()) {
        redirect_with_message('animais_novo.php', 'Lote inválido para a fazenda ativa.', 'danger');
    }
}

if ($maeId) {
    $stmtMae = db()->prepare('SELECT 1 FROM animais WHERE id = :id AND fazenda_id = :fazenda_id');
    $stmtMae->execute([':id' => $maeId, ':fazenda_id' => $fazendaId]);
    if (!$stmtMae->fetchColumn()) {
        redirect_with_message('animais_novo.php', 'Mãe inválida para a fazenda ativa.', 'danger');
    }
}

$sql = 'INSERT INTO animais (fazenda_id, numero_brinco, raca, categoria_atual, status_reprodutivo, lote_id, etapa_atual, retiro_atual, data_nasc, sexo, mae_id)
        VALUES (:fazenda_id, :numero_brinco, :raca, :categoria_atual, :status_reprodutivo, :lote_id, :etapa_atual, :retiro_atual, :data_nasc, :sexo, :mae_id)';

try {
    $stmt = db()->prepare($sql);
    $stmt->execute([
        ':fazenda_id' => $fazendaId,
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
    ]);
} catch (PDOException $e) {
    if ((string) $e->getCode() === '23505') {
        redirect_with_message('animais_novo.php', 'Número de brinco já cadastrado nesta fazenda.', 'danger');
    }
    redirect_with_message('animais_novo.php', 'Erro ao salvar animal.', 'danger');
}

redirect_with_message('animais_listar.php', 'Animal cadastrado com sucesso.');
