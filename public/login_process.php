<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

verify_csrf_or_abort();

$email = trim((string) ($_POST['email'] ?? ''));
$senha = (string) ($_POST['senha'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
    redirect_with_message('login.php', 'Informe e-mail e senha válidos.', 'danger');
}

if (is_login_rate_limited($email)) {
    log_audit('login_rate_limited', null, 'email=' . strtolower($email));
    redirect_with_message('login.php', 'Muitas tentativas. Aguarde 15 minutos e tente novamente.', 'danger');
}

$stmt = db()->prepare('SELECT id, nome, email, senha_hash, perfil, ativo FROM usuarios WHERE email = :email LIMIT 1');
$stmt->execute([':email' => strtolower($email)]);
$usuario = $stmt->fetch();

if (!$usuario || !$usuario['ativo'] || !password_verify($senha, $usuario['senha_hash'])) {
    register_login_attempt($email, false);
    log_audit('login_fail', null, 'email=' . strtolower($email));
    redirect_with_message('login.php', 'Credenciais inválidas.', 'danger');
}

clear_login_attempts($email);
register_login_attempt($email, true);

$_SESSION['usuario'] = [
    'id' => (int) $usuario['id'],
    'nome' => $usuario['nome'],
    'email' => $usuario['email'],
    'perfil' => $usuario['perfil'],
];

session_regenerate_id(true);
ensure_active_fazenda();
log_audit('login_success', current_fazenda_id());
redirect_with_message('index.php', 'Login realizado com sucesso.');
