<?php
function render_content(){
    global $funcionarios, $q, $status;
    ?>
    <h2>📋 Funcionários</h2>
    
    <div class="btn-group" style="margin-bottom:20px; text-align:center;">
        <a href="/rh.php" class="theme-btn" style="display:inline-block; min-width:150px; text-align:center;">← Voltar</a>
    </div>
    
    <form method="GET" class="form-inline">
        <input type="text" name="q" value="<?= htmlspecialchars($q ?? '') ?>" placeholder="Pesquisar">
        <select name="status">
            <option value="">Todos</option>
            <option value="0" <?= (!empty($status) && $status==='0') ? 'selected' : '' ?>>Pendente</option>
            <option value="1" <?= (!empty($status) && $status==='1') ? 'selected' : '' ?>>Resgatado</option>
        </select>
        <button type="submit" class="btn-primary">Filtrar</button>
    </form>

    <div class="table-wrap" style="margin-top:12px;">
        <table class="responsive-table">
            <thead><tr><th>Nome</th><th>CPF</th><th>Matrícula</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach($funcionarios as $f): ?>
            <tr class="<?= $f['brinde_status'] ? 'status-resgatado' : 'status-pendente' ?>">
                <td data-label="Nome"><?= htmlspecialchars($f['nome_completo']) ?></td>
                <td data-label="CPF"><?= htmlspecialchars($f['cpf']) ?></td>
                <td data-label="Matrícula"><?= htmlspecialchars($f['matricula']) ?></td>
                <td data-label="Status">
                    <?php if($f['brinde_status']): ?>
                        <span class="badge-resgatado">✅ Resgatado</span>
                    <?php else: ?>
                        <span class="badge-pendente">❌ Pendente</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

include __DIR__ . '/base.php';
