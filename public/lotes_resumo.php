<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

$fazendaId = require_active_fazenda();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirect_with_message('lotes_listar.php', 'ID inválido.', 'danger');
}

$stmt = db()->prepare('SELECT * FROM lotes WHERE id = :id AND fazenda_id = :fazenda_id');
$stmt->execute([':id' => $id, ':fazenda_id' => $fazendaId]);
$lote = $stmt->fetch();
if (!$lote) {
    redirect_with_message('lotes_listar.php', 'Lote não encontrado.', 'danger');
}

$stmtAnimais = db()->prepare('SELECT id, numero_brinco, categoria_atual, status_reprodutivo FROM animais WHERE fazenda_id = :fazenda_id AND lote_id = :lote_id ORDER BY numero_brinco');
$stmtAnimais->execute([':fazenda_id' => $fazendaId, ':lote_id' => $id]);
$animais = $stmtAnimais->fetchAll();

render_layout_start('Resumo do lote');
?>
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <h5><?= e($lote['nome']) ?></h5>
        <p class="mb-1"><strong>Etapa:</strong> <?= e($lote['etapa']) ?></p>
        <p class="mb-1"><strong>Retiro:</strong> <?= e($lote['retiro']) ?></p>
        <p class="mb-0"><strong>Descrição:</strong> <?= e($lote['descricao']) ?></p>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h6>Animais no lote (<?= count($animais) ?>)</h6>
        <table class="table table-sm">
            <thead><tr><th>Brinco</th><th>Categoria</th><th>Status Reprodutivo</th></tr></thead>
            <tbody>
                <?php foreach ($animais as $animal): ?>
                    <tr><td><?= e($animal['numero_brinco']) ?></td><td><?= e($animal['categoria_atual']) ?></td><td><?= e($animal['status_reprodutivo']) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php render_layout_end(); ?>
