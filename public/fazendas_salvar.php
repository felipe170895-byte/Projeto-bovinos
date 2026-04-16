<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: fazendas_listar.php');
    exit;
}

$nome = trim((string) ($_POST['nome'] ?? ''));
$responsavel = trim((string) ($_POST['responsavel'] ?? ''));
$cidade = trim((string) ($_POST['cidade'] ?? ''));
$uf = strtoupper(trim((string) ($_POST['uf'] ?? '')));
$telefone = trim((string) ($_POST['telefone'] ?? ''));
$observacoes = trim((string) ($_POST['observacoes'] ?? ''));

if ($nome === '') {
    redirect_with_message('fazendas_nova.php', 'Nome da fazenda é obrigatório.', 'danger');
}

$pdo = db();
$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare('INSERT INTO fazendas (nome, responsavel, cidade, uf, telefone, observacoes) VALUES (:nome, :responsavel, :cidade, :uf, :telefone, :observacoes) RETURNING id');
    $stmt->execute([
        ':nome' => $nome,
        ':responsavel' => $responsavel ?: null,
        ':cidade' => $cidade ?: null,
        ':uf' => $uf ?: null,
        ':telefone' => $telefone ?: null,
        ':observacoes' => $observacoes ?: null,
    ]);

    $fazendaId = (int) $stmt->fetchColumn();
    $usuarioId = (int) current_user()['id'];

    $stmtUf = $pdo->prepare('INSERT INTO usuarios_fazendas (usuario_id, fazenda_id) VALUES (:usuario_id, :fazenda_id) ON CONFLICT DO NOTHING');
    $stmtUf->execute([':usuario_id' => $usuarioId, ':fazenda_id' => $fazendaId]);

    $_SESSION['fazenda_id'] = $fazendaId;
    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    redirect_with_message('fazendas_nova.php', 'Erro ao salvar fazenda.', 'danger');
}

redirect_with_message('fazendas_listar.php', 'Fazenda criada com sucesso.');
