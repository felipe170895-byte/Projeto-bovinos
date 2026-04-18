<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin_fazendas.php');
    exit;
}

verify_csrf_or_abort();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirect_with_message('admin_fazendas.php', 'ID inválido.', 'danger');
}

$stmt = db()->prepare('DELETE FROM fazendas WHERE id = :id');
$stmt->execute([':id' => $id]);

redirect_with_message('admin_fazendas.php', 'Fazenda excluída com sucesso.');
