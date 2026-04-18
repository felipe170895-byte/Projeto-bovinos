<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin_usuarios.php');
    exit;
}

verify_csrf_or_abort();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirect_with_message('admin_usuarios.php', 'ID inválido.', 'danger');
}

if ($id === (int) current_user()['id']) {
    redirect_with_message('admin_usuarios.php', 'Você não pode inativar seu próprio usuário.', 'danger');
}

$stmt = db()->prepare('UPDATE usuarios SET ativo = NOT ativo, updated_at = NOW() WHERE id = :id');
$stmt->execute([':id' => $id]);

redirect_with_message('admin_usuarios.php', 'Status do usuário atualizado.');
