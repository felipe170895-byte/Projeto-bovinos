<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

require_auth();
render_layout_start('Nova fazenda');
?>
<form method="post" action="fazendas_salvar.php" class="card shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Nome</label><input class="form-control" name="nome" required maxlength="120"></div>
            <div class="col-md-6"><label class="form-label">Responsável</label><input class="form-control" name="responsavel" maxlength="120"></div>
            <div class="col-md-4"><label class="form-label">Cidade</label><input class="form-control" name="cidade" maxlength="100"></div>
            <div class="col-md-2"><label class="form-label">UF</label><input class="form-control" name="uf" maxlength="2"></div>
            <div class="col-md-6"><label class="form-label">Telefone</label><input class="form-control" name="telefone" maxlength="20"></div>
            <div class="col-md-12"><label class="form-label">Observações</label><textarea class="form-control" name="observacoes" rows="3"></textarea></div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex gap-2">
        <button class="btn btn-primary">Salvar</button>
        <a href="fazendas_listar.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
<?php render_layout_end(); ?>
