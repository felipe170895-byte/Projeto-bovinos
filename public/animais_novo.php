<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

$fazendaId = require_active_fazenda();
$lotes = db()->prepare('SELECT id, nome FROM lotes WHERE fazenda_id = :fazenda_id AND ativo = TRUE ORDER BY nome');
$lotes->execute([':fazenda_id' => $fazendaId]);
$lotes = $lotes->fetchAll();

$matrizes = db()->prepare("SELECT id, numero_brinco FROM animais WHERE fazenda_id = :fazenda_id AND sexo = 'F' ORDER BY numero_brinco");
$matrizes->execute([':fazenda_id' => $fazendaId]);
$matrizes = $matrizes->fetchAll();

render_layout_start('Novo animal');
?>
<form method="post" action="animais_salvar.php" class="card shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Número do brinco *</label><input name="numero_brinco" class="form-control" required maxlength="50"></div>
            <div class="col-md-4"><label class="form-label">Raça</label><input name="raca" class="form-control" maxlength="80"></div>
            <div class="col-md-4"><label class="form-label">Categoria atual</label><input name="categoria_atual" class="form-control" maxlength="80"></div>
            <div class="col-md-4"><label class="form-label">Status reprodutivo</label><input name="status_reprodutivo" class="form-control" maxlength="80"></div>
            <div class="col-md-4"><label class="form-label">Lote</label><select name="lote_id" class="form-select"><option value="">Selecione...</option><?php foreach ($lotes as $lote): ?><option value="<?= (int) $lote['id'] ?>"><?= e($lote['nome']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label">Sexo</label><select name="sexo" class="form-select"><option value="">Selecione...</option><option value="F">Fêmea</option><option value="M">Macho</option></select></div>
            <div class="col-md-4"><label class="form-label">Data nascimento</label><input type="date" name="data_nasc" class="form-control"></div>
            <div class="col-md-4"><label class="form-label">Etapa atual</label><input name="etapa_atual" class="form-control" maxlength="80"></div>
            <div class="col-md-4"><label class="form-label">Retiro atual</label><input name="retiro_atual" class="form-control" maxlength="80"></div>
            <div class="col-md-4"><label class="form-label">Mãe</label><select name="mae_id" class="form-select"><option value="">Não informar</option><?php foreach ($matrizes as $m): ?><option value="<?= (int) $m['id'] ?>">Brinco <?= e($m['numero_brinco']) ?></option><?php endforeach; ?></select></div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex gap-2">
        <button class="btn btn-primary">Salvar</button>
        <a href="animais_listar.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
<?php render_layout_end(); ?>
