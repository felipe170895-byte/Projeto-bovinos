<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

$fazendaId = require_active_fazenda();
$animalId = filter_input(INPUT_GET, 'animal_id', FILTER_VALIDATE_INT);
if (!$animalId) {
    redirect_with_message('eventos_escolher_animal.php', 'Selecione um animal válido.', 'danger');
}

$stmtAnimal = db()->prepare('SELECT id, numero_brinco FROM animais WHERE id = :id AND fazenda_id = :fazenda_id');
$stmtAnimal->execute([':id' => $animalId, ':fazenda_id' => $fazendaId]);
$animal = $stmtAnimal->fetch();
if (!$animal) {
    redirect_with_message('eventos_escolher_animal.php', 'Animal não encontrado para a fazenda ativa.', 'danger');
}

$stmtLotes = db()->prepare('SELECT id, nome FROM lotes WHERE fazenda_id = :fazenda_id ORDER BY nome');
$stmtLotes->execute([':fazenda_id' => $fazendaId]);
$lotes = $stmtLotes->fetchAll();

render_layout_start('Novo evento reprodutivo');
?>
<form method="post" action="eventos_salvar.php" class="card shadow-sm">
    <?= csrf_input() ?>
    <input type="hidden" name="animal_id" value="<?= (int) $animal['id'] ?>">
    <div class="card-body">
        <p class="mb-3"><strong>Animal:</strong> Brinco <?= e($animal['numero_brinco']) ?></p>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Tipo evento *</label><select name="tipo_evento" class="form-select" required><option value="">Selecione...</option><option value="cio">Cio</option><option value="inseminacao">Inseminação</option><option value="diagnostico_gestacao">Diagnóstico de gestação</option><option value="parto">Parto</option><option value="secagem">Secagem</option><option value="outro">Outro</option></select></div>
            <div class="col-md-4"><label class="form-label">Data do evento *</label><input type="date" name="data_evento" class="form-control" required value="<?= date('Y-m-d') ?>"></div>
            <div class="col-md-4"><label class="form-label">Lote</label><select name="lote_id" class="form-select"><option value="">Sem lote</option><?php foreach ($lotes as $lote): ?><option value="<?= (int) $lote['id'] ?>"><?= e($lote['nome']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label">Etapa</label><input name="etapa" class="form-control" maxlength="80"></div>
            <div class="col-md-4"><label class="form-label">Resultado</label><input name="resultado" class="form-control" maxlength="120"></div>
            <div class="col-md-12"><label class="form-label">Detalhes</label><textarea name="detalhes" class="form-control" rows="3"></textarea></div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex gap-2">
        <button class="btn btn-primary">Salvar evento</button>
        <a href="eventos_listar.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
<?php render_layout_end(); ?>
