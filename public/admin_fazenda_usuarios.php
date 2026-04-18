<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

require_admin();

$fazendaId = filter_input(INPUT_GET, 'fazenda_id', FILTER_VALIDATE_INT);
if (!$fazendaId) {
    redirect_with_message('admin_fazendas.php', 'Fazenda inválida.', 'danger');
}

$stmtFazenda = db()->prepare('SELECT id, nome FROM fazendas WHERE id = :id');
$stmtFazenda->execute([':id' => $fazendaId]);
$fazenda = $stmtFazenda->fetch();

if (!$fazenda) {
    redirect_with_message('admin_fazendas.php', 'Fazenda não encontrada.', 'danger');
}

$stmtVinculados = db()->prepare('SELECT u.id, u.nome, u.email FROM usuarios_fazendas uf INNER JOIN usuarios u ON u.id = uf.usuario_id WHERE uf.fazenda_id = :fazenda_id ORDER BY u.nome');
$stmtVinculados->execute([':fazenda_id' => $fazendaId]);
$vinculados = $stmtVinculados->fetchAll();

$usuarios = db()->query('SELECT id, nome, email FROM usuarios WHERE ativo = TRUE ORDER BY nome')->fetchAll();

render_layout_start('Admin - Vínculos da fazenda');
?>
<h5 class="mb-3">Fazenda: <?= e($fazenda['nome']) ?></h5>
<form method="post" action="admin_fazenda_usuario_add.php" class="card card-body mb-3">
    <?= csrf_input() ?>
    <input type="hidden" name="fazenda_id" value="<?= (int) $fazenda['id'] ?>">
    <div class="row g-2 align-items-end">
        <div class="col-md-8">
            <label class="form-label">Usuário</label>
            <select name="usuario_id" class="form-select" required>
                <option value="">Selecione...</option>
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?= (int) $u['id'] ?>"><?= e($u['nome']) ?> (<?= e($u['email']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4"><button class="btn btn-primary w-100">Vincular usuário</button></div>
    </div>
</form>

<table class="table table-striped bg-white shadow-sm">
    <thead><tr><th>Nome</th><th>E-mail</th><th>Ação</th></tr></thead>
    <tbody>
    <?php foreach ($vinculados as $u): ?>
        <tr>
            <td><?= e($u['nome']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td>
                <form method="post" action="admin_fazenda_usuario_remover.php" onsubmit="return confirm('Remover vínculo?');">
    <?= csrf_input() ?>
                    <input type="hidden" name="fazenda_id" value="<?= (int) $fazenda['id'] ?>">
                    <input type="hidden" name="usuario_id" value="<?= (int) $u['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger">Remover</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php render_layout_end(); ?>
