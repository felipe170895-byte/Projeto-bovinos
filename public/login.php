<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

render_layout_start('Entrar', false);
?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-3">Acessar o sistema</h4>
                <form method="post" action="login_process.php">
                    <div class="mb-3">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <input type="password" name="senha" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100">Entrar</button>
                </form>
                <p class="mt-3 mb-0 text-muted">Não tem conta? <a href="register.php">Cadastre-se</a></p>
            </div>
        </div>
    </div>
</div>
<?php render_layout_end(false); ?>
