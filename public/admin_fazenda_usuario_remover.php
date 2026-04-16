<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin_fazendas.php');
    exit;
}

$fazendaId = filter_input(INPUT_POST, 'fazenda_id', FILTER_VALIDATE_INT);
$usuarioId = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);

if (!$fazendaId || !$usuarioId) {
    redirect_with_message('admin_fazendas.php', 'Dados inválidos.', 'danger');
}

$stmt = db()->prepare('DELETE FROM usuarios_fazendas WHERE usuario_id = :usuario_id AND fazenda_id = :fazenda_id');
$stmt->execute([':usuario_id' => $usuarioId, ':fazenda_id' => $fazendaId]);

redirect_with_message('admin_fazenda_usuarios.php?fazenda_id=' . $fazendaId, 'Vínculo removido.');
