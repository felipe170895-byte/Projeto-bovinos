<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

require_auth();
ensure_active_fazenda();

$user = current_user();
$fazendaId = current_fazenda_id();

$totalAnimais = 0;
$totalLotes = 0;
$totalEventos = 0;

if ($fazendaId !== null) {
    $stmtAnimais = db()->prepare('SELECT COUNT(*) FROM animais WHERE fazenda_id = :fazenda_id');
    $stmtAnimais->execute([':fazenda_id' => $fazendaId]);
    $totalAnimais = (int) $stmtAnimais->fetchColumn();

    $stmtLotes = db()->prepare('SELECT COUNT(*) FROM lotes WHERE fazenda_id = :fazenda_id AND ativo = TRUE');
    $stmtLotes->execute([':fazenda_id' => $fazendaId]);
    $totalLotes = (int) $stmtLotes->fetchColumn();

    $stmtEventos = db()->prepare('SELECT COUNT(*) FROM eventos_reprodutivos er INNER JOIN animais a ON a.id = er.animal_id WHERE a.fazenda_id = :fazenda_id');
    $stmtEventos->execute([':fazenda_id' => $fazendaId]);
    $totalEventos = (int) $stmtEventos->fetchColumn();
}

render_layout_start('Dashboard');
?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-soft shadow-sm"><div class="card-body"><h6>Animais</h6><p class="display-6 mb-0"><?= $totalAnimais ?></p></div></div>
    </div>
    <div class="col-md-4">
        <div class="card card-soft shadow-sm"><div class="card-body"><h6>Lotes ativos</h6><p class="display-6 mb-0"><?= $totalLotes ?></p></div></div>
    </div>
    <div class="col-md-4">
        <div class="card card-soft shadow-sm"><div class="card-body"><h6>Eventos reprodutivos</h6><p class="display-6 mb-0"><?= $totalEventos ?></p></div></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4"><a class="card text-decoration-none text-dark shadow-sm" href="fazendas_listar.php"><div class="card-body"><h6>Gerenciar Fazendas</h6><p class="text-muted mb-0">Criar, editar e trocar fazenda ativa.</p></div></a></div>
    <div class="col-md-4"><a class="card text-decoration-none text-dark shadow-sm" href="animais_listar.php"><div class="card-body"><h6>Animais</h6><p class="text-muted mb-0">Cadastro e acompanhamento do rebanho.</p></div></a></div>
    <div class="col-md-4"><a class="card text-decoration-none text-dark shadow-sm" href="eventos_listar.php"><div class="card-body"><h6>Eventos</h6><p class="text-muted mb-0">Registro reprodutivo e histórico.</p></div></a></div>
    <div class="col-md-4"><a class="card text-decoration-none text-dark shadow-sm" href="lotes_listar.php"><div class="card-body"><h6>Lotes</h6><p class="text-muted mb-0">Organização por etapa e retiro.</p></div></a></div>
    <div class="col-md-4"><a class="card text-decoration-none text-dark shadow-sm" href="bezerros_listar.php"><div class="card-body"><h6>Bezerros</h6><p class="text-muted mb-0">Controle de nascimentos.</p></div></a></div>
    <div class="col-md-4"><a class="card text-decoration-none text-dark shadow-sm" href="relatorios.php"><div class="card-body"><h6>Relatórios</h6><p class="text-muted mb-0">Visão rápida de indicadores.</p></div></a></div>
</div>
<?php render_layout_end(); ?>
