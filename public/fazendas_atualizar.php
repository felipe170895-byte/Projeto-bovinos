<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

require_auth();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: fazendas_listar.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nome = trim((string) ($_POST['nome'] ?? ''));
$responsavel = trim((string) ($_POST['responsavel'] ?? ''));
$cidade = trim((string) ($_POST['cidade'] ?? ''));
$uf = strtoupper(trim((string) ($_POST['uf'] ?? '')));
$telefone = trim((string) ($_POST['telefone'] ?? ''));
$observacoes = trim((string) ($_POST['observacoes'] ?? ''));
$ativa = isset($_POST['ativa']) ? (int) $_POST['ativa'] : 1;

if (!$id || $nome === '') {
    redirect_with_message('fazendas_listar.php', 'Dados inválidos.', 'danger');
}

if (!is_admin()) {
    $check = db()->prepare('SELECT 1 FROM usuarios_fazendas WHERE usuario_id = :usuario_id AND fazenda_id = :fazenda_id');
    $check->execute([':usuario_id' => (int) current_user()['id'], ':fazenda_id' => $id]);
    if (!$check->fetchColumn()) {
        redirect_with_message('fazendas_listar.php', 'Acesso negado para esta fazenda.', 'danger');
    }
}

if (is_admin()) {
    $sql = 'UPDATE fazendas SET nome = :nome, responsavel = :responsavel, cidade = :cidade, uf = :uf, telefone = :telefone, observacoes = :observacoes, ativa = :ativa, updated_at = NOW() WHERE id = :id';
} else {
    $sql = 'UPDATE fazendas SET nome = :nome, responsavel = :responsavel, cidade = :cidade, uf = :uf, telefone = :telefone, observacoes = :observacoes, updated_at = NOW() WHERE id = :id';
}

$params = [
    ':id' => $id,
    ':nome' => $nome,
    ':responsavel' => $responsavel ?: null,
    ':cidade' => $cidade ?: null,
    ':uf' => $uf ?: null,
    ':telefone' => $telefone ?: null,
    ':observacoes' => $observacoes ?: null,
];
if (is_admin()) {
    $params[':ativa'] = $ativa === 1;
}

$stmt = db()->prepare($sql);
$stmt->execute($params);

redirect_with_message('fazendas_listar.php', 'Fazenda atualizada.');
