<?php
function render_content(){
    global $funcionarios, $q, $status;
    ?>
    <h2>📋 Funcionários</h2>
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
            <tr>
                <td><?= htmlspecialchars($f['nome_completo']) ?></td>
                <td><?= htmlspecialchars($f['cpf']) ?></td>
                <td><?= htmlspecialchars($f['matricula']) ?></td>
                <td><?= $f['brinde_status'] ? 'Resgatado' : 'Pendente' ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="btn-group" style="margin-top:20px;"><a href="/rh.php" class="theme-btn">Voltar</a></div>
    <?php
}

include __DIR__ . '/base.php';
