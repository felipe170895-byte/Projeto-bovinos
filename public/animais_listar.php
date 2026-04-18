<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

$fazendaId = require_active_fazenda();

$stmt = db()->prepare('SELECT a.id, a.numero_brinco, a.raca, a.categoria_atual, a.status_reprodutivo, l.nome AS lote_nome, a.sexo, a.data_nasc
                       FROM animais a
                       LEFT JOIN lotes l ON l.id = a.lote_id
                       WHERE a.fazenda_id = :fazenda_id
                       ORDER BY a.numero_brinco');
$stmt->execute([':fazenda_id' => $fazendaId]);
$animais = $stmt->fetchAll();

render_layout_start('Animais');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Animais da fazenda ativa</h5>
    <a href="animais_novo.php" class="btn btn-primary">Novo animal</a>
</div>

<table class="table table-striped bg-white shadow-sm">
    <thead>
        <tr>
            <th>Brinco</th><th>Raça</th><th>Categoria</th><th>Status Reprodutivo</th><th>Lote</th><th>Sexo</th><th>Nascimento</th><th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($animais as $animal): ?>
            <tr>
                <td><?= e($animal['numero_brinco']) ?></td>
                <td><?= e($animal['raca']) ?></td>
                <td><?= e($animal['categoria_atual']) ?></td>
                <td><?= e($animal['status_reprodutivo']) ?></td>
                <td><?= e($animal['lote_nome']) ?></td>
                <td><?= e($animal['sexo']) ?></td>
                <td><?= e($animal['data_nasc']) ?></td>
                <td>
                    <a href="animais_detalhes.php?id=<?= (int) $animal['id'] ?>" class="btn btn-sm btn-outline-secondary">Detalhes</a>
                    <a href="animais_editar.php?id=<?= (int) $animal['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php render_layout_end(); ?>
