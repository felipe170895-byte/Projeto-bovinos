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

render_layout_start('Editar lote');
?>
<form method="post" action="lotes_atualizar.php" class="card shadow-sm">
    <input type="hidden" name="id" value="<?= (int) $lote['id'] ?>">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Nome *</label><input name="nome" class="form-control" required value="<?= e($lote['nome']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Etapa</label><input name="etapa" class="form-control" value="<?= e($lote['etapa']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Retiro</label><input name="retiro" class="form-control" value="<?= e($lote['retiro']) ?>"></div>
            <div class="col-md-12"><label class="form-label">Descrição</label><textarea name="descricao" class="form-control" rows="3"><?= e($lote['descricao']) ?></textarea></div>
            <div class="col-md-3"><label class="form-label">Status</label><select name="ativo" class="form-select"><option value="1" <?= $lote['ativo'] ? 'selected' : '' ?>>Ativo</option><option value="0" <?= !$lote['ativo'] ? 'selected' : '' ?>>Inativo</option></select></div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex gap-2">
        <button class="btn btn-primary">Atualizar</button>
        <a href="lotes_listar.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
<?php render_layout_end(); ?>
