<?php

declare(strict_types=1);

require_once __DIR__ . '/../auth.php';

function render_layout_start(string $title, bool $authenticated = true): void
{
    apply_security_headers();

    $user = current_user();
    $flash = flash();
    $fazendas = [];

    if ($authenticated && $user) {
        ensure_active_fazenda();
        $fazendas = user_fazendas((int) $user['id']);
    }
    ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111827">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <link rel="manifest" href="/manifest.webmanifest">
    <title><?= e($title) ?> | ReproBov</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body>
<?php if ($authenticated && $user): ?>
<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-lg-2 p-3 sidebar">
            <h5 class="mb-1">ReproBov</h5>
            <p class="small text-light-emphasis mb-4">Gestão reprodutiva bovina</p>
            <nav class="nav flex-column gap-1">
                <a class="nav-link" href="index.php">Dashboard</a>
                <a class="nav-link" href="fazendas_listar.php">Fazendas</a>
                <a class="nav-link" href="animais_listar.php">Animais</a>
                <a class="nav-link" href="lotes_listar.php">Lotes</a>
                <a class="nav-link" href="eventos_listar.php">Eventos</a>
                <a class="nav-link" href="bezerros_listar.php">Bezerros</a>
                <a class="nav-link" href="relatorios.php">Relatórios</a>
                <a class="nav-link" href="scan_brinco.php">Scanner de Brinco</a>
                <?php if (is_admin()): ?>
                <hr class="border-secondary">
                <a class="nav-link" href="admin_usuarios.php">Admin Usuários</a>
                <a class="nav-link" href="admin_fazendas.php">Admin Fazendas</a>
                <?php endif; ?>
                <form method="post" action="logout.php" class="mt-2">
                    <?= csrf_input() ?>
                    <button type="submit" class="nav-link btn btn-link text-start w-100 p-0">Sair</button>
                </form>
            </nav>
        </aside>
        <main class="col-md-9 col-lg-10 p-0">
            <nav class="navbar navbar-expand-lg bg-white border-bottom px-3 py-2">
                <span class="navbar-brand mb-0 h1"><?= e($title) ?></span>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <form method="post" action="fazenda_trocar.php" class="d-flex gap-2">
                        <?= csrf_input() ?>
                        <select name="fazenda_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <?php foreach ($fazendas as $fazenda): ?>
                            <option value="<?= (int) $fazenda['id'] ?>" <?= current_fazenda_id() === (int) $fazenda['id'] ? 'selected' : '' ?>>
                                <?= e($fazenda['nome']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <span class="badge badge-soft"><?= e($user['perfil']) ?></span>
                    <span class="text-muted small"><?= e($user['nome']) ?></span>
                </div>
            </nav>
            <div class="p-4">
                <?php if ($flash): ?>
                    <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
                <?php endif; ?>
<?php else: ?>
<div class="container py-5">
    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>
<?php endif; ?>
<?php
}

function render_layout_end(bool $authenticated = true): void
{
    ?>
<?php if ($authenticated && is_logged_in()): ?>
            </div>
        </main>
    </div>
</div>
<?php else: ?>
</div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
<?php
}
