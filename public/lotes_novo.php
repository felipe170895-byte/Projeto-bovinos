<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

require_active_fazenda();
render_layout_start('Novo lote');
?>
<form method="post" action="lotes_salvar.php" class="card shadow-sm">
    <?= csrf_input() ?>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Nome *</label><input name="nome" class="form-control" required maxlength="100"></div>
            <div class="col-md-4"><label class="form-label">Etapa</label><input name="etapa" class="form-control" maxlength="80"></div>
            <div class="col-md-4"><label class="form-label">Retiro</label><input name="retiro" class="form-control" maxlength="100"></div>
            <div class="col-md-12"><label class="form-label">Descrição</label><textarea name="descricao" class="form-control" rows="3"></textarea></div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex gap-2">
        <button class="btn btn-primary">Salvar</button>
        <a href="lotes_listar.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
<?php render_layout_end(); ?>
