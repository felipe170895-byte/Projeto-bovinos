<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

$fazendaId = require_active_fazenda();
$stmt = db()->prepare('SELECT id, nome, etapa, retiro, descricao, ativo FROM lotes WHERE fazenda_id = :fazenda_id ORDER BY nome');
$stmt->execute([':fazenda_id' => $fazendaId]);
$lotes = $stmt->fetchAll();

render_layout_start('Lotes');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Lotes da fazenda ativa</h5>
    <a href="lotes_novo.php" class="btn btn-primary">Novo lote</a>
</div>
<table class="table table-striped bg-white shadow-sm">
    <thead><tr><th>Nome</th><th>Etapa</th><th>Retiro</th><th>Status</th><th>Ações</th></tr></thead>
    <tbody>
        <?php foreach ($lotes as $lote): ?>
            <tr>
                <td><?= e($lote['nome']) ?></td>
                <td><?= e($lote['etapa']) ?></td>
                <td><?= e($lote['retiro']) ?></td>
                <td><?= $lote['ativo'] ? 'Ativo' : 'Inativo' ?></td>
                <td>
                    <a href="lotes_resumo.php?id=<?= (int) $lote['id'] ?>" class="btn btn-sm btn-outline-secondary">Resumo</a>
                    <a href="lotes_editar.php?id=<?= (int) $lote['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php render_layout_end(); ?>
