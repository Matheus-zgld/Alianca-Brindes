<?php
function render_content(){
    global $nome, $qr_url, $qr_content;
    ?>
    <h2>✅ QR Code Gerado</h2>
    <p style="text-align:center;">Olá, <strong><?= htmlspecialchars($nome) ?></strong>! Apresente este código no local de retirada do brinde.</p>

    <div class="qr-box">
        <p>Seu Código de Resgate:</p>
        <div id="qrcode" style="margin:0 auto; width: fit-content;"></div>
        <p style="font-weight:bold; color: var(--primary-color); margin:12px 0;">CÓDIGO: <?= htmlspecialchars($qr_content) ?></p>
        <p style="font-size:0.9em; color: var(--dark-text);">Você pode gerar este código quantas vezes precisar.</p>
    </div>

    <div class="btn-group" style="margin-top:20px;">
        <a href="/" class="theme-btn">Gerar Novamente</a>
    </div>
    
    <!-- Client-side QR generation (uses CDN for qrcodejs). Falls back to showing external URL if JS is disabled. -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        (function(){
            var code = <?= json_encode($qr_content) ?>;
            try{
                var qr = new QRCode(document.getElementById('qrcode'), {
                    text: code,
                    width: 280,
                    height: 280,
                    colorDark : '#000000',
                    colorLight : '#ffffff',
                    correctLevel : QRCode.CorrectLevel.H
                });
            } catch(e) {
                // If QR generation fails, optionally show an external image link
                var container = document.getElementById('qrcode');
                container.innerHTML = '<a href="' + <?= json_encode($qr_url) ?> + '" target="_blank">Abrir QR (imagem)</a>';
            }
        })();
    </script>
    <?php
}

include __DIR__ . '/base.php';
