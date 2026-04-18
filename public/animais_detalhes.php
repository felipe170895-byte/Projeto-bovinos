<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

$fazendaId = require_active_fazenda();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirect_with_message('animais_listar.php', 'ID inválido.', 'danger');
}

$stmt = db()->prepare('SELECT a.*, l.nome AS lote_nome, m.numero_brinco AS mae_brinco
                       FROM animais a
                       LEFT JOIN lotes l ON l.id = a.lote_id
                       LEFT JOIN animais m ON m.id = a.mae_id
                       WHERE a.id = :id AND a.fazenda_id = :fazenda_id');
$stmt->execute([':id' => $id, ':fazenda_id' => $fazendaId]);
$animal = $stmt->fetch();
if (!$animal) {
    redirect_with_message('animais_listar.php', 'Animal não encontrado.', 'danger');
}

$eventos = db()->prepare('SELECT id, tipo_evento, data_evento, resultado FROM eventos_reprodutivos WHERE animal_id = :animal_id ORDER BY data_evento DESC');
$eventos->execute([':animal_id' => $id]);
$eventos = $eventos->fetchAll();

render_layout_start('Detalhes do animal');
?>
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <h5 class="mb-3">Brinco <?= e($animal['numero_brinco']) ?></h5>
        <div class="row g-2">
            <div class="col-md-3"><strong>Raça:</strong> <?= e($animal['raca']) ?></div>
            <div class="col-md-3"><strong>Categoria:</strong> <?= e($animal['categoria_atual']) ?></div>
            <div class="col-md-3"><strong>Status:</strong> <?= e($animal['status_reprodutivo']) ?></div>
            <div class="col-md-3"><strong>Lote:</strong> <?= e($animal['lote_nome']) ?></div>
            <div class="col-md-3"><strong>Sexo:</strong> <?= e($animal['sexo']) ?></div>
            <div class="col-md-3"><strong>Nascimento:</strong> <?= e($animal['data_nasc']) ?></div>
            <div class="col-md-3"><strong>Mãe:</strong> <?= e($animal['mae_brinco']) ?></div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h6>Eventos reprodutivos</h6>
        <table class="table table-sm">
            <thead><tr><th>Data</th><th>Tipo</th><th>Resultado</th></tr></thead>
            <tbody>
                <?php foreach ($eventos as $evento): ?>
                    <tr><td><?= e($evento['data_evento']) ?></td><td><?= e($evento['tipo_evento']) ?></td><td><?= e($evento['resultado']) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php render_layout_end(); ?>
