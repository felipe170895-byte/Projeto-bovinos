<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

$fazendaId = require_active_fazenda();
$stmt = db()->prepare("SELECT id, numero_brinco FROM animais WHERE fazenda_id = :fazenda_id AND sexo = 'F' ORDER BY numero_brinco");
$stmt->execute([':fazenda_id' => $fazendaId]);
$maes = $stmt->fetchAll();

render_layout_start('Novo bezerro');
?>
<form method="post" action="bezerros_salvar.php" class="card shadow-sm">
    <?= csrf_input() ?>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Brinco *</label><input name="numero_brinco" class="form-control" required maxlength="50"></div>
            <div class="col-md-4"><label class="form-label">Nascimento *</label><input type="date" name="data_nasc" class="form-control" required value="<?= date('Y-m-d') ?>"></div>
            <div class="col-md-4"><label class="form-label">Sexo</label><select name="sexo" class="form-select"><option value="">Selecione...</option><option value="F">Fêmea</option><option value="M">Macho</option></select></div>
            <div class="col-md-4"><label class="form-label">Peso ao nascer (kg)</label><input type="number" step="0.01" min="0" name="peso_nasc_kg" class="form-control"></div>
            <div class="col-md-8"><label class="form-label">Mãe</label><select name="animal_mae_id" class="form-select"><option value="">Não informar</option><?php foreach ($maes as $mae): ?><option value="<?= (int) $mae['id'] ?>">Brinco <?= e($mae['numero_brinco']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-12"><label class="form-label">Observações</label><textarea name="observacoes" class="form-control" rows="3"></textarea></div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex gap-2">
        <button class="btn btn-primary">Salvar</button>
        <a href="bezerros_listar.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
<?php render_layout_end(); ?>
