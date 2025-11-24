<?php
function render_content(){
    global $resgatados, $error;
    ?>
    <h2>🔑 Área de Baixa (RH)</h2>
    <p style="text-align:center;">Insira o código ou use a câmera para dar baixa no brinde.</p>

    <?php if(!empty($_SESSION['rh_user'])): ?>
    <div style="text-align:center; margin-bottom:10px; font-size:0.9em; color: var(--primary-color);"><strong>Conectado como:</strong> <?= htmlspecialchars($_SESSION['rh_user']) ?></div>
    <?php endif; ?>

    <?php if(!empty($error)): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <div id="reader" style="width:100%; max-width:400px; margin:16px auto;"></div>

    <form method="POST" id="manual-form" style="text-align:center;">
        <label for="qr_data" style="display:block; text-align:left; font-weight:600;">Conteúdo do QR Code</label>
        <input type="text" id="qr_data" name="qr_data" placeholder="Ex: 00000000000:123456" required>
        <button type="submit" class="btn-primary" style="width:100%; margin-top:8px;">Verificar Brinde</button>
    </form>

    <div class="btn-group" style="margin-top:20px;">
        <a href="/" class="theme-btn">Acesso Funcionário</a>
        <a href="/rh_logs.php" class="theme-btn">Ver Logs</a>
        <a href="/rh_funcionarios.php" class="theme-btn">Verificar Funcionários</a>
        <a href="/rh_logout.php" class="theme-btn" style="background:#c00;">Sair da Conta</a>
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
                <td data-label="Data"><?= htmlspecialchars(explode(' ', $func['data_resgate'])[0] ?? '') ?></td>
                <td data-label="Hora"><?= htmlspecialchars(explode(' ', $func['data_resgate'])[1] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center; color:var(--primary-color);">Nenhum brinde foi resgatado ainda.</p>
    <?php endif; ?>
    </div>

    <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            document.getElementById('qr_data').value = decodedText;
            document.getElementById('manual-form').submit();
            html5QrcodeScanner.clear();
        }
        function onScanFailure(error) {}
        let html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps:10, qrbox:{width:250, height:250}, facingMode:"environment" }, false);
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    </script>
    <?php
}

include __DIR__ . '/base.php';
