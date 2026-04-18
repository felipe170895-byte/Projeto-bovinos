<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$fazendaId = require_active_fazenda();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: scan_brinco.php');
    exit;
}

verify_csrf_or_abort();

$codigo = trim((string) ($_POST['codigo_brinco'] ?? ''));
if ($codigo === '') {
    redirect_with_message('scan_brinco.php', 'Código inválido.', 'danger');
}

$stmt = db()->prepare('SELECT id FROM animais WHERE fazenda_id = :fazenda_id AND numero_brinco = :numero_brinco LIMIT 1');
$stmt->execute([
    ':fazenda_id' => $fazendaId,
    ':numero_brinco' => $codigo,
]);
$animalId = $stmt->fetchColumn();

if (!$animalId) {
    redirect_with_message('scan_brinco.php', 'Brinco não encontrado na fazenda ativa.', 'warning');
}

header('Location: animais_detalhes.php?id=' . (int) $animalId);
exit;
