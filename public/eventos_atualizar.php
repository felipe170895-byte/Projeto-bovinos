<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$fazendaId = require_active_fazenda();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: eventos_listar.php');
    exit;
}

verify_csrf_or_abort();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$tipoEvento = trim((string) ($_POST['tipo_evento'] ?? ''));
$dataEvento = (string) ($_POST['data_evento'] ?? '');
$etapa = trim((string) ($_POST['etapa'] ?? ''));
$loteId = filter_input(INPUT_POST, 'lote_id', FILTER_VALIDATE_INT);
$resultado = trim((string) ($_POST['resultado'] ?? ''));
$detalhes = trim((string) ($_POST['detalhes'] ?? ''));

$tiposValidos = ['cio', 'inseminacao', 'diagnostico_gestacao', 'parto', 'secagem', 'outro'];
if (!$id || !in_array($tipoEvento, $tiposValidos, true) || $dataEvento === '') {
    redirect_with_message('eventos_listar.php', 'Dados inválidos.', 'danger');
}

$stmtEvento = db()->prepare('SELECT er.id FROM eventos_reprodutivos er INNER JOIN animais a ON a.id = er.animal_id WHERE er.id = :id AND a.fazenda_id = :fazenda_id');
$stmtEvento->execute([':id' => $id, ':fazenda_id' => $fazendaId]);
if (!$stmtEvento->fetchColumn()) {
    redirect_with_message('eventos_listar.php', 'Evento não pertence à fazenda ativa.', 'danger');
}

if ($loteId) {
    $stmtLote = db()->prepare('SELECT 1 FROM lotes WHERE id = :id AND fazenda_id = :fazenda_id');
    $stmtLote->execute([':id' => $loteId, ':fazenda_id' => $fazendaId]);
    if (!$stmtLote->fetchColumn()) {
        redirect_with_message('eventos_editar.php?id=' . $id, 'Lote inválido para a fazenda ativa.', 'danger');
    }
}

$stmt = db()->prepare('UPDATE eventos_reprodutivos
                       SET tipo_evento = :tipo_evento,
                           data_evento = :data_evento,
                           etapa = :etapa,
                           lote_id = :lote_id,
                           resultado = :resultado,
                           detalhes = :detalhes,
                           updated_at = NOW()
                       WHERE id = :id');
$stmt->execute([
    ':id' => $id,
    ':tipo_evento' => $tipoEvento,
    ':data_evento' => $dataEvento,
    ':etapa' => $etapa ?: null,
    ':lote_id' => $loteId ?: null,
    ':resultado' => $resultado ?: null,
    ':detalhes' => $detalhes ?: null,
]);

redirect_with_message('eventos_listar.php', 'Evento atualizado com sucesso.');
