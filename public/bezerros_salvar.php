<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$fazendaId = require_active_fazenda();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: bezerros_listar.php');
    exit;
}

$numeroBrinco = trim((string) ($_POST['numero_brinco'] ?? ''));
$dataNasc = (string) ($_POST['data_nasc'] ?? '');
$sexo = (string) ($_POST['sexo'] ?? '');
$pesoNascKg = trim((string) ($_POST['peso_nasc_kg'] ?? ''));
$animalMaeId = filter_input(INPUT_POST, 'animal_mae_id', FILTER_VALIDATE_INT);
$observacoes = trim((string) ($_POST['observacoes'] ?? ''));

if ($numeroBrinco === '' || $dataNasc === '') {
    redirect_with_message('bezerros_novo.php', 'Brinco e data de nascimento são obrigatórios.', 'danger');
}

if ($animalMaeId) {
    $stmtMae = db()->prepare('SELECT 1 FROM animais WHERE id = :id AND fazenda_id = :fazenda_id');
    $stmtMae->execute([':id' => $animalMaeId, ':fazenda_id' => $fazendaId]);
    if (!$stmtMae->fetchColumn()) {
        redirect_with_message('bezerros_novo.php', 'Mãe inválida para a fazenda ativa.', 'danger');
    }
}

try {
    $stmt = db()->prepare('INSERT INTO bezerros (fazenda_id, animal_mae_id, numero_brinco, sexo, data_nasc, peso_nasc_kg, observacoes)
                           VALUES (:fazenda_id, :animal_mae_id, :numero_brinco, :sexo, :data_nasc, :peso_nasc_kg, :observacoes)');
    $stmt->execute([
        ':fazenda_id' => $fazendaId,
        ':animal_mae_id' => $animalMaeId ?: null,
        ':numero_brinco' => $numeroBrinco,
        ':sexo' => in_array($sexo, ['M', 'F'], true) ? $sexo : null,
        ':data_nasc' => $dataNasc,
        ':peso_nasc_kg' => $pesoNascKg !== '' ? $pesoNascKg : null,
        ':observacoes' => $observacoes ?: null,
    ]);
} catch (PDOException $e) {
    if ((string) $e->getCode() === '23505') {
        redirect_with_message('bezerros_novo.php', 'Brinco de bezerro já cadastrado para esta fazenda.', 'danger');
    }
    redirect_with_message('bezerros_novo.php', 'Erro ao salvar bezerro.', 'danger');
}

redirect_with_message('bezerros_listar.php', 'Bezerro cadastrado com sucesso.');
