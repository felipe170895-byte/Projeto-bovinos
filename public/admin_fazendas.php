<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

require_admin();
$fazendas = db()->query('SELECT id, nome, responsavel, ativa FROM fazendas ORDER BY nome')->fetchAll();

render_layout_start('Admin - Fazendas');
?>
<table class="table table-striped bg-white shadow-sm">
    <thead><tr><th>Nome</th><th>Responsável</th><th>Status</th><th>Ações</th></tr></thead>
    <tbody>
    <?php foreach ($fazendas as $fazenda): ?>
        <tr>
            <td><?= e($fazenda['nome']) ?></td>
            <td><?= e($fazenda['responsavel']) ?></td>
            <td><?= $fazenda['ativa'] ? 'Ativa' : 'Inativa' ?></td>
            <td>
                <a href="admin_fazenda_usuarios.php?fazenda_id=<?= (int) $fazenda['id'] ?>" class="btn btn-sm btn-outline-secondary">Usuários</a>
                <form method="post" action="admin_fazenda_excluir.php" class="d-inline" onsubmit="return confirm('Excluir fazenda?');">
    <?= csrf_input() ?>
                    <input type="hidden" name="id" value="<?= (int) $fazenda['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php render_layout_end(); ?>
