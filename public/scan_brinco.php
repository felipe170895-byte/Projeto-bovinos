<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

require_active_fazenda();
render_layout_start('Scanner de brinco');
?>
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="mb-3">Leitura de brinco por código de barras</h5>
        <p class="text-muted">Use um leitor USB (modo teclado) e confirme o código para buscar o animal.</p>
        <form method="post" action="scan_brinco_process.php" class="row g-2">
            <div class="col-md-8">
                <label class="form-label">Código lido</label>
                <input name="codigo_brinco" class="form-control form-control-lg" required autofocus maxlength="50" placeholder="Ex.: BR12345">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-primary w-100 btn-lg">Buscar animal</button>
            </div>
        </form>
    </div>
</div>
<?php render_layout_end(); ?>
