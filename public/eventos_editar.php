<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

$fazendaId = require_active_fazenda();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirect_with_message('eventos_listar.php', 'ID inválido.', 'danger');
}

$stmt = db()->prepare('SELECT er.*, a.numero_brinco, a.fazenda_id
                       FROM eventos_reprodutivos er
                       INNER JOIN animais a ON a.id = er.animal_id
                       WHERE er.id = :id AND a.fazenda_id = :fazenda_id');
$stmt->execute([':id' => $id, ':fazenda_id' => $fazendaId]);
$evento = $stmt->fetch();
if (!$evento) {
    redirect_with_message('eventos_listar.php', 'Evento não encontrado.', 'danger');
}

$stmtLotes = db()->prepare('SELECT id, nome FROM lotes WHERE fazenda_id = :fazenda_id ORDER BY nome');
$stmtLotes->execute([':fazenda_id' => $fazendaId]);
$lotes = $stmtLotes->fetchAll();

render_layout_start('Editar evento');
?>
<form method="post" action="eventos_atualizar.php" class="card shadow-sm">
    <?= csrf_input() ?>
    <input type="hidden" name="id" value="<?= (int) $evento['id'] ?>">
    <div class="card-body">
        <p class="mb-3"><strong>Animal:</strong> Brinco <?= e($evento['numero_brinco']) ?></p>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Tipo evento *</label><select name="tipo_evento" class="form-select" required><?php foreach (['cio' => 'Cio', 'inseminacao' => 'Inseminação', 'diagnostico_gestacao' => 'Diagnóstico de gestação', 'parto' => 'Parto', 'secagem' => 'Secagem', 'outro' => 'Outro'] as $k => $v): ?><option value="<?= $k ?>" <?= $evento['tipo_evento'] === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label">Data do evento *</label><input type="date" name="data_evento" class="form-control" required value="<?= e($evento['data_evento']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Lote</label><select name="lote_id" class="form-select"><option value="">Sem lote</option><?php foreach ($lotes as $lote): ?><option value="<?= (int) $lote['id'] ?>" <?= (int) ($evento['lote_id'] ?? 0) === (int) $lote['id'] ? 'selected' : '' ?>><?= e($lote['nome']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label">Etapa</label><input name="etapa" class="form-control" value="<?= e($evento['etapa']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Resultado</label><input name="resultado" class="form-control" value="<?= e($evento['resultado']) ?>"></div>
            <div class="col-md-12"><label class="form-label">Detalhes</label><textarea name="detalhes" class="form-control" rows="3"><?= e($evento['detalhes']) ?></textarea></div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex gap-2">
        <button class="btn btn-primary">Atualizar evento</button>
        <a href="eventos_listar.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
<?php render_layout_end(); ?>
