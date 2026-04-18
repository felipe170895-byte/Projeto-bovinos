<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

render_layout_start('Cadastro', false);
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-3">Criar conta</h4>
                <form method="post" action="register_process.php">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" required maxlength="120">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <input type="password" name="senha" class="form-control" required minlength="6">
                    </div>
                    <button class="btn btn-success w-100">Criar conta</button>
                </form>
                <p class="mt-3 mb-0 text-muted">Já possui conta? <a href="login.php">Entrar</a></p>
            </div>
        </div>
    </div>
</div>
<?php render_layout_end(false); ?>
