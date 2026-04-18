<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

require_auth();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: fazendas_listar.php');
    exit;
}

verify_csrf_or_abort();

$fazendaId = filter_input(INPUT_POST, 'fazenda_id', FILTER_VALIDATE_INT);
if (!$fazendaId) {
    redirect_with_message('fazendas_listar.php', 'Fazenda inválida.', 'danger');
}

$check = db()->prepare('SELECT 1 FROM usuarios_fazendas uf INNER JOIN fazendas f ON f.id = uf.fazenda_id WHERE uf.usuario_id = :usuario_id AND uf.fazenda_id = :fazenda_id AND f.ativa = TRUE');
$check->execute([':usuario_id' => (int) current_user()['id'], ':fazenda_id' => $fazendaId]);

if (!$check->fetchColumn()) {
    redirect_with_message('fazendas_listar.php', 'Você não possui acesso a esta fazenda.', 'danger');
}

$_SESSION['fazenda_id'] = $fazendaId;
redirect_with_message('index.php', 'Fazenda ativa atualizada.');
