<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

$fazendaId = require_active_fazenda();

$stmt = db()->prepare('SELECT id, numero_brinco, categoria_atual FROM animais WHERE fazenda_id = :fazenda_id ORDER BY numero_brinco');
$stmt->execute([':fazenda_id' => $fazendaId]);
$animais = $stmt->fetchAll();

render_layout_start('Novo evento - escolher animal');
?>
<form method="get" action="eventos_novo.php" class="card card-body shadow-sm">
    <label class="form-label">Selecione o animal</label>
    <select name="animal_id" class="form-select mb-3" required>
        <option value="">Selecione...</option>
        <?php foreach ($animais as $animal): ?>
            <option value="<?= (int) $animal['id'] ?>">Brinco <?= e($animal['numero_brinco']) ?><?= $animal['categoria_atual'] ? ' - ' . e($animal['categoria_atual']) : '' ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn btn-primary">Continuar</button>
</form>
<?php render_layout_end(); ?>
