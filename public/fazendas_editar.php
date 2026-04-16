<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

require_auth();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirect_with_message('fazendas_listar.php', 'ID inválido.', 'danger');
}

$user = current_user();
if (!is_admin()) {
    $check = db()->prepare('SELECT 1 FROM usuarios_fazendas WHERE usuario_id = :usuario_id AND fazenda_id = :fazenda_id');
    $check->execute([':usuario_id' => (int) $user['id'], ':fazenda_id' => $id]);
    if (!$check->fetchColumn()) {
        redirect_with_message('fazendas_listar.php', 'Fazenda não vinculada ao usuário.', 'danger');
    }
}

$stmt = db()->prepare('SELECT * FROM fazendas WHERE id = :id');
$stmt->execute([':id' => $id]);
$fazenda = $stmt->fetch();

if (!$fazenda) {
    redirect_with_message('fazendas_listar.php', 'Fazenda não encontrada.', 'danger');
}

render_layout_start('Editar fazenda');
?>
<form method="post" action="fazendas_atualizar.php" class="card shadow-sm">
    <input type="hidden" name="id" value="<?= (int) $fazenda['id'] ?>">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Nome</label><input class="form-control" name="nome" required value="<?= e($fazenda['nome']) ?>"></div>
            <div class="col-md-6"><label class="form-label">Responsável</label><input class="form-control" name="responsavel" value="<?= e($fazenda['responsavel']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Cidade</label><input class="form-control" name="cidade" value="<?= e($fazenda['cidade']) ?>"></div>
            <div class="col-md-2"><label class="form-label">UF</label><input class="form-control" name="uf" maxlength="2" value="<?= e($fazenda['uf']) ?>"></div>
            <div class="col-md-6"><label class="form-label">Telefone</label><input class="form-control" name="telefone" value="<?= e($fazenda['telefone']) ?>"></div>
            <div class="col-md-12"><label class="form-label">Observações</label><textarea class="form-control" name="observacoes" rows="3"><?= e($fazenda['observacoes']) ?></textarea></div>
            <?php if (is_admin()): ?>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="ativa" class="form-select">
                    <option value="1" <?= $fazenda['ativa'] ? 'selected' : '' ?>>Ativa</option>
                    <option value="0" <?= !$fazenda['ativa'] ? 'selected' : '' ?>>Inativa</option>
                </select>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-footer bg-white d-flex gap-2">
        <button class="btn btn-primary">Atualizar</button>
        <a href="fazendas_listar.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
<?php render_layout_end(); ?>
