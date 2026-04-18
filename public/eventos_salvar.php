<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$fazendaId = require_active_fazenda();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: eventos_listar.php');
    exit;
}

$animalId = filter_input(INPUT_POST, 'animal_id', FILTER_VALIDATE_INT);
$tipoEvento = trim((string) ($_POST['tipo_evento'] ?? ''));
$dataEvento = (string) ($_POST['data_evento'] ?? '');
$etapa = trim((string) ($_POST['etapa'] ?? ''));
$loteId = filter_input(INPUT_POST, 'lote_id', FILTER_VALIDATE_INT);
$resultado = trim((string) ($_POST['resultado'] ?? ''));
$detalhes = trim((string) ($_POST['detalhes'] ?? ''));

$tiposValidos = ['cio', 'inseminacao', 'diagnostico_gestacao', 'parto', 'secagem', 'outro'];
if (!$animalId || !in_array($tipoEvento, $tiposValidos, true) || $dataEvento === '') {
    redirect_with_message('eventos_escolher_animal.php', 'Dados inválidos para o evento.', 'danger');
}

$stmtAnimal = db()->prepare('SELECT id FROM animais WHERE id = :id AND fazenda_id = :fazenda_id');
$stmtAnimal->execute([':id' => $animalId, ':fazenda_id' => $fazendaId]);
if (!$stmtAnimal->fetchColumn()) {
    redirect_with_message('eventos_escolher_animal.php', 'Animal não pertence à fazenda ativa.', 'danger');
}

if ($loteId) {
    $stmtLote = db()->prepare('SELECT 1 FROM lotes WHERE id = :id AND fazenda_id = :fazenda_id');
    $stmtLote->execute([':id' => $loteId, ':fazenda_id' => $fazendaId]);
    if (!$stmtLote->fetchColumn()) {
        redirect_with_message('eventos_escolher_animal.php', 'Lote inválido para a fazenda ativa.', 'danger');
    }
}

$stmt = db()->prepare('INSERT INTO eventos_reprodutivos (animal_id, tipo_evento, data_evento, etapa, lote_id, resultado, detalhes)
                       VALUES (:animal_id, :tipo_evento, :data_evento, :etapa, :lote_id, :resultado, :detalhes)');
$stmt->execute([
    ':animal_id' => $animalId,
    ':tipo_evento' => $tipoEvento,
    ':data_evento' => $dataEvento,
    ':etapa' => $etapa ?: null,
    ':lote_id' => $loteId ?: null,
    ':resultado' => $resultado ?: null,
    ':detalhes' => $detalhes ?: null,
]);

redirect_with_message('eventos_listar.php', 'Evento cadastrado com sucesso.');
