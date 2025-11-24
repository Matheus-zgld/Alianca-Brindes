<?php
function render_content(){
    global $status, $data;
    ?>
    <h2>🚫 Brinde Resgatado</h2>
    <p style="text-align:center;">Seu brinde já foi resgatado e registrado no sistema.</p>

    <div style="text-align:center; margin:16px 0; padding:16px;">
        <p>Status: <strong style="color: green; font-size:1.1em;"><?= htmlspecialchars($status) ?></strong></p>
        <p>Data do Resgate: <strong><?= htmlspecialchars($data) ?></strong></p>
        <p style="margin-top:16px;">Obrigado e Boas Festas!</p>
    </div>

    <div class="btn-group" style="margin-top:20px;"><a href="/" class="theme-btn">Voltar</a></div>
    <?php
}

include __DIR__ . '/base.php';
