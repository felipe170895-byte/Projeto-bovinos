<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$nome = trim((string) ($_POST['nome'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$senha = (string) ($_POST['senha'] ?? '');

if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($senha) < 6) {
    redirect_with_message('register.php', 'Dados inválidos. Verifique os campos.', 'danger');
}

$pdo = db();
$check = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email');
$check->execute([':email' => $email]);
if ($check->fetch()) {
    redirect_with_message('register.php', 'E-mail já cadastrado.', 'danger');
}

$stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES (:nome, :email, :senha_hash, :perfil)');
$stmt->execute([
    ':nome' => $nome,
    ':email' => $email,
    ':senha_hash' => password_hash($senha, PASSWORD_DEFAULT),
    ':perfil' => 'funcionario',
]);

redirect_with_message('login.php', 'Conta criada com sucesso. Faça o login.');
