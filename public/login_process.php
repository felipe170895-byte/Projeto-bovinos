<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = trim((string) ($_POST['email'] ?? ''));
$senha = (string) ($_POST['senha'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
    redirect_with_message('login.php', 'Informe e-mail e senha válidos.', 'danger');
}

$stmt = db()->prepare('SELECT id, nome, email, senha_hash, perfil, ativo FROM usuarios WHERE email = :email LIMIT 1');
$stmt->execute([':email' => $email]);
$usuario = $stmt->fetch();

if (!$usuario || !$usuario['ativo'] || !password_verify($senha, $usuario['senha_hash'])) {
    redirect_with_message('login.php', 'Credenciais inválidas.', 'danger');
}

$_SESSION['usuario'] = [
    'id' => (int) $usuario['id'],
    'nome' => $usuario['nome'],
    'email' => $usuario['email'],
    'perfil' => $usuario['perfil'],
];

ensure_active_fazenda();
redirect_with_message('index.php', 'Login realizado com sucesso.');
