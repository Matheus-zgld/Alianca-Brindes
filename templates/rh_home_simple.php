<?php
function render_content(){
    global $resgatados, $error;
    ?>
    <h2>🔑 Área de Baixa (RH)</h2>
    <p style="text-align:center;">Escaneie o QR Code ou insira o código manualmente.</p>

    <?php if(!empty($_SESSION['rh_user'])): ?>
    <div style="text-align:center; margin-bottom:15px; font-size:0.9em; color: var(--primary-color);"><strong>Conectado como:</strong> <?= htmlspecialchars($_SESSION['rh_user']) ?></div>
    <?php endif; ?>

    <?php if(!empty($error)): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <!-- Entrada Manual -->
    <div style="margin: 20px 0;">
        <h3 style="text-align:center; color: var(--primary-color);">⌨️ Insira o código do QR</h3>
        <form method="POST" id="manual-form">
            <label for="qr_data" style="display:block; text-align:left; font-weight:600; margin-top:12px;">Conteúdo do QR Code</label>
            <input type="text" id="qr_data" name="qr_data" placeholder="Ex: 00000000000:123456" required style="width:100%;">
            <button type="submit" class="btn-primary" style="margin-top:12px; padding:14px;">Verificar Brinde</button>
        </form>
    </div>

    <div class="btn-group" style="margin-top:30px; text-align:center; display:flex; flex-wrap:wrap; justify-content:center; gap:10px;">
        <a href="index.php" class="theme-btn" style="flex:1; min-width:150px; text-align:center;">Acesso Funcionário</a>
        <a href="rh_logs.php" class="theme-btn" style="flex:1; min-width:150px; text-align:center;">Ver Logs</a>
        <a href="rh_funcionarios.php" class="theme-btn" style="flex:1; min-width:150px; text-align:center;">Verificar Funcionários</a>
        <a href="rh_logout.php" class="theme-btn" style="flex:1; min-width:150px; background:#dc3545; text-align:center;">Sair da Conta</a>
    </div>

    <hr style="border-top:1px solid var(--primary-color); margin:20px 0;">

    <h3 style="color:var(--primary-color); margin-top:10px;">✅ Brindes Entregues (<?= count($resgatados) ?>)</h3>

    <div class="table-wrap">
    <?php if($resgatados): ?>
        <table class="responsive-table">
            <thead><tr><th>Nome</th><th>Matrícula</th><th>Data</th><th>Hora</th></tr></thead>
            <tbody>
            <?php foreach($resgatados as $func): ?>
            <tr>
                <td data-label="Nome"><?= htmlspecialchars($func['nome_completo']) ?></td>
                <td data-label="Matrícula"><?= htmlspecialchars($func['matricula']) ?></td>
                <td data-label="Data"><?php $dr = explode(' ', $func['data_resgate'])[0] ?? ''; echo htmlspecialchars($dr ? date('d-m-y', strtotime($dr)) : ''); ?></td>
                <td data-label="Hora"><?= htmlspecialchars(explode(' ', $func['data_resgate'])[1] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center; color:var(--primary-color);">Nenhum brinde foi resgatado ainda.</p>
    <?php endif; ?>
    </div>
    <?php
}

include __DIR__ . '/base.php';
