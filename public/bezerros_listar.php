<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

$fazendaId = require_active_fazenda();
$stmt = db()->prepare('SELECT b.id, b.numero_brinco, b.sexo, b.data_nasc, b.peso_nasc_kg, a.numero_brinco AS mae_brinco
                       FROM bezerros b
                       LEFT JOIN animais a ON a.id = b.animal_mae_id
                       WHERE b.fazenda_id = :fazenda_id
                       ORDER BY b.data_nasc DESC, b.id DESC');
$stmt->execute([':fazenda_id' => $fazendaId]);
$bezerros = $stmt->fetchAll();

render_layout_start('Bezerros');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Bezerros registrados</h5>
    <a href="bezerros_novo.php" class="btn btn-primary">Novo bezerro</a>
</div>

<table class="table table-striped bg-white shadow-sm">
    <thead><tr><th>Brinco</th><th>Nascimento</th><th>Sexo</th><th>Peso (kg)</th><th>Mãe</th></tr></thead>
    <tbody>
        <?php foreach ($bezerros as $bezerro): ?>
            <tr>
                <td><?= e($bezerro['numero_brinco']) ?></td>
                <td><?= e($bezerro['data_nasc']) ?></td>
                <td><?= e($bezerro['sexo']) ?></td>
                <td><?= e($bezerro['peso_nasc_kg']) ?></td>
                <td><?= e($bezerro['mae_brinco']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php render_layout_end(); ?>
