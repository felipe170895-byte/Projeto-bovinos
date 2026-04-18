<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

require_auth();
$user = current_user();

if (is_admin()) {
    $stmt = db()->query('SELECT id, nome, responsavel, cidade, uf, ativa FROM fazendas ORDER BY nome');
    $fazendas = $stmt->fetchAll();
} else {
    $stmt = db()->prepare('SELECT f.id, f.nome, f.responsavel, f.cidade, f.uf, f.ativa FROM usuarios_fazendas uf INNER JOIN fazendas f ON f.id = uf.fazenda_id WHERE uf.usuario_id = :usuario_id ORDER BY f.nome');
    $stmt->execute([':usuario_id' => (int) $user['id']]);
    $fazendas = $stmt->fetchAll();
}

render_layout_start('Fazendas');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Fazendas vinculadas</h5>
    <a href="fazendas_nova.php" class="btn btn-primary">Nova fazenda</a>
</div>
<table class="table table-striped bg-white shadow-sm">
    <thead><tr><th>Nome</th><th>Responsável</th><th>Cidade/UF</th><th>Status</th><th>Ações</th></tr></thead>
    <tbody>
    <?php foreach ($fazendas as $fazenda): ?>
        <tr>
            <td><?= e($fazenda['nome']) ?></td>
            <td><?= e($fazenda['responsavel']) ?></td>
            <td><?= e(trim(($fazenda['cidade'] ?? '') . '/' . ($fazenda['uf'] ?? ''), '/')) ?></td>
            <td><?= $fazenda['ativa'] ? 'Ativa' : 'Inativa' ?></td>
            <td>
                <a class="btn btn-sm btn-outline-secondary" href="fazendas_editar.php?id=<?= (int) $fazenda['id'] ?>">Editar</a>
                <form method="post" action="fazenda_trocar.php" class="d-inline">
    <?= csrf_input() ?>
                    <input type="hidden" name="fazenda_id" value="<?= (int) $fazenda['id'] ?>">
                    <button class="btn btn-sm btn-outline-primary">Selecionar</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php render_layout_end(); ?>
