<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

$fazendaId = require_active_fazenda();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirect_with_message('animais_listar.php', 'ID inválido.', 'danger');
}

$stmt = db()->prepare('SELECT * FROM animais WHERE id = :id AND fazenda_id = :fazenda_id');
$stmt->execute([':id' => $id, ':fazenda_id' => $fazendaId]);
$animal = $stmt->fetch();
if (!$animal) {
    redirect_with_message('animais_listar.php', 'Animal não encontrado para a fazenda ativa.', 'danger');
}

$lotes = db()->prepare('SELECT id, nome FROM lotes WHERE fazenda_id = :fazenda_id ORDER BY nome');
$lotes->execute([':fazenda_id' => $fazendaId]);
$lotes = $lotes->fetchAll();

$matrizes = db()->prepare("SELECT id, numero_brinco FROM animais WHERE fazenda_id = :fazenda_id AND sexo = 'F' AND id <> :id ORDER BY numero_brinco");
$matrizes->execute([':fazenda_id' => $fazendaId, ':id' => $id]);
$matrizes = $matrizes->fetchAll();

render_layout_start('Editar animal');
?>
<form method="post" action="animais_atualizar.php" class="card shadow-sm">
    <?= csrf_input() ?>
    <input type="hidden" name="id" value="<?= (int) $animal['id'] ?>">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Número do brinco *</label><input name="numero_brinco" class="form-control" required value="<?= e($animal['numero_brinco']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Raça</label><input name="raca" class="form-control" value="<?= e($animal['raca']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Categoria atual</label><input name="categoria_atual" class="form-control" value="<?= e($animal['categoria_atual']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Status reprodutivo</label><input name="status_reprodutivo" class="form-control" value="<?= e($animal['status_reprodutivo']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Lote</label><select name="lote_id" class="form-select"><option value="">Sem lote</option><?php foreach ($lotes as $lote): ?><option value="<?= (int) $lote['id'] ?>" <?= (int) ($animal['lote_id'] ?? 0) === (int) $lote['id'] ? 'selected' : '' ?>><?= e($lote['nome']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label">Sexo</label><select name="sexo" class="form-select"><option value="">Selecione...</option><option value="F" <?= $animal['sexo'] === 'F' ? 'selected' : '' ?>>Fêmea</option><option value="M" <?= $animal['sexo'] === 'M' ? 'selected' : '' ?>>Macho</option></select></div>
            <div class="col-md-4"><label class="form-label">Data nascimento</label><input type="date" name="data_nasc" class="form-control" value="<?= e($animal['data_nasc']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Etapa atual</label><input name="etapa_atual" class="form-control" value="<?= e($animal['etapa_atual']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Retiro atual</label><input name="retiro_atual" class="form-control" value="<?= e($animal['retiro_atual']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Mãe</label><select name="mae_id" class="form-select"><option value="">Não informar</option><?php foreach ($matrizes as $m): ?><option value="<?= (int) $m['id'] ?>" <?= (int) ($animal['mae_id'] ?? 0) === (int) $m['id'] ? 'selected' : '' ?>>Brinco <?= e($m['numero_brinco']) ?></option><?php endforeach; ?></select></div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex gap-2">
        <button class="btn btn-primary">Atualizar</button>
        <a href="animais_listar.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
<?php render_layout_end(); ?>
