<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

$fazendaId = require_active_fazenda();

$stmt = db()->prepare('SELECT COUNT(*) FROM animais WHERE fazenda_id = :fazenda_id');
$stmt->execute([':fazenda_id' => $fazendaId]);
$totalAnimais = (int) $stmt->fetchColumn();

$stmt = db()->prepare('SELECT COUNT(*) FROM bezerros WHERE fazenda_id = :fazenda_id');
$stmt->execute([':fazenda_id' => $fazendaId]);
$totalBezerros = (int) $stmt->fetchColumn();

$stmt = db()->prepare('SELECT COUNT(*) FROM lotes WHERE fazenda_id = :fazenda_id AND ativo = TRUE');
$stmt->execute([':fazenda_id' => $fazendaId]);
$totalLotesAtivos = (int) $stmt->fetchColumn();

$sqlTipo = "SELECT er.tipo_evento, COUNT(*) AS total
            FROM eventos_reprodutivos er
            INNER JOIN animais a ON a.id = er.animal_id
            WHERE a.fazenda_id = :fazenda_id
              AND er.data_evento >= (CURRENT_DATE - INTERVAL '90 days')
            GROUP BY er.tipo_evento
            ORDER BY total DESC";
$stmt = db()->prepare($sqlTipo);
$stmt->execute([':fazenda_id' => $fazendaId]);
$eventosPorTipo = $stmt->fetchAll();

$sqlMes = "SELECT DATE_TRUNC('month', er.data_evento) AS mes, COUNT(*) AS total
           FROM eventos_reprodutivos er
           INNER JOIN animais a ON a.id = er.animal_id
           WHERE a.fazenda_id = :fazenda_id
             AND er.data_evento >= (CURRENT_DATE - INTERVAL '12 months')
           GROUP BY DATE_TRUNC('month', er.data_evento)
           ORDER BY mes DESC";
$stmt = db()->prepare($sqlMes);
$stmt->execute([':fazenda_id' => $fazendaId]);
$eventosPorMes = $stmt->fetchAll();

render_layout_start('Relatórios simples');
?>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card card-soft shadow-sm"><div class="card-body"><h6>Total de animais</h6><p class="display-6 mb-0"><?= $totalAnimais ?></p></div></div></div>
    <div class="col-md-4"><div class="card card-soft shadow-sm"><div class="card-body"><h6>Total de bezerros</h6><p class="display-6 mb-0"><?= $totalBezerros ?></p></div></div></div>
    <div class="col-md-4"><div class="card card-soft shadow-sm"><div class="card-body"><h6>Lotes ativos</h6><p class="display-6 mb-0"><?= $totalLotesAtivos ?></p></div></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6 class="mb-3">Eventos por tipo (últimos 90 dias)</h6>
                <table class="table table-sm">
                    <thead><tr><th>Tipo</th><th>Total</th></tr></thead>
                    <tbody>
                        <?php foreach ($eventosPorTipo as $item): ?>
                            <tr><td><?= e($item['tipo_evento']) ?></td><td><?= (int) $item['total'] ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6 class="mb-3">Eventos por mês (12 meses)</h6>
                <table class="table table-sm">
                    <thead><tr><th>Mês</th><th>Total</th></tr></thead>
                    <tbody>
                        <?php foreach ($eventosPorMes as $item): ?>
                            <tr><td><?= e(date('m/Y', strtotime((string) $item['mes']))) ?></td><td><?= (int) $item['total'] ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php render_layout_end(); ?>
