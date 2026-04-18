<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

verify_csrf_or_abort();

$nome = trim((string) ($_POST['nome'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$senha = (string) ($_POST['senha'] ?? '');

$senhaForte = strlen($senha) >= 8 && preg_match('/[A-Z]/', $senha) && preg_match('/[a-z]/', $senha) && preg_match('/\d/', $senha);

if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$senhaForte) {
    redirect_with_message('register.php', 'Use nome válido, e-mail válido e senha forte (8+ caracteres com maiúscula, minúscula e número).', 'danger');
}

$pdo = db();
$check = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email');
$check->execute([':email' => strtolower($email)]);
if ($check->fetch()) {
    redirect_with_message('register.php', 'E-mail já cadastrado.', 'danger');
}

$stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES (:nome, :email, :senha_hash, :perfil)');
$stmt->execute([
    ':nome' => $nome,
    ':email' => strtolower($email),
    ':senha_hash' => password_hash($senha, PASSWORD_DEFAULT),
    ':perfil' => 'funcionario',
]);

log_audit('usuario_registrado', null, 'email=' . strtolower($email));

redirect_with_message('login.php', 'Conta criada com sucesso. Faça o login.');
