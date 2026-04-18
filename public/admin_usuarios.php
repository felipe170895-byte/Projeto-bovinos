<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

require_admin();

$sql = 'SELECT u.id, u.nome, u.email, u.perfil, u.ativo, f.nome AS fazenda_padrao
        FROM usuarios u
        LEFT JOIN fazendas f ON f.id = u.fazenda_id
        ORDER BY u.nome';
$usuarios = db()->query($sql)->fetchAll();

render_layout_start('Admin - Usuários');
?>
<table class="table table-striped bg-white shadow-sm">
    <thead><tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Fazenda padrão</th><th>Status</th><th>Ações</th></tr></thead>
    <tbody>
    <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?= e($u['nome']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td><?= e($u['perfil']) ?></td>
            <td><?= e($u['fazenda_padrao']) ?></td>
            <td><?= $u['ativo'] ? 'Ativo' : 'Inativo' ?></td>
            <td>
                <form method="post" action="admin_usuarios_toggle.php" class="d-inline">
    <?= csrf_input() ?>
                    <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                    <button class="btn btn-sm btn-outline-warning">Ativar/Inativar</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php render_layout_end(); ?>
