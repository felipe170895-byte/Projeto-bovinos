<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

$fazendaId = require_active_fazenda();

$stmt = db()->prepare('SELECT er.id, er.tipo_evento, er.data_evento, er.resultado, a.numero_brinco, l.nome AS lote_nome
                       FROM eventos_reprodutivos er
                       INNER JOIN animais a ON a.id = er.animal_id
                       LEFT JOIN lotes l ON l.id = er.lote_id
                       WHERE a.fazenda_id = :fazenda_id
                       ORDER BY er.data_evento DESC, er.id DESC');
$stmt->execute([':fazenda_id' => $fazendaId]);
$eventos = $stmt->fetchAll();

render_layout_start('Eventos reprodutivos');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Eventos da fazenda ativa</h5>
    <a href="eventos_escolher_animal.php" class="btn btn-primary">Novo evento</a>
</div>
<table class="table table-striped bg-white shadow-sm">
    <thead><tr><th>Data</th><th>Animal</th><th>Tipo</th><th>Lote</th><th>Resultado</th><th>Ações</th></tr></thead>
    <tbody>
    <?php foreach ($eventos as $evento): ?>
        <tr>
            <td><?= e($evento['data_evento']) ?></td>
            <td>Brinco <?= e($evento['numero_brinco']) ?></td>
            <td><?= e($evento['tipo_evento']) ?></td>
            <td><?= e($evento['lote_nome']) ?></td>
            <td><?= e($evento['resultado']) ?></td>
            <td><a href="eventos_editar.php?id=<?= (int) $evento['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php render_layout_end(); ?>
