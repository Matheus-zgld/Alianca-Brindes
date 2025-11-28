<?php
function render_content(){
    global $resgatados, $error;
    ?>
    <h2>🔑 Área de Baixa (RH)</h2>
    <p style="text-align:center;">Escaneie o QR Code com a câmera ou insira o código manualmente.</p>

    <?php if(!empty($_SESSION['rh_user'])): ?>
    <div style="text-align:center; margin-bottom:15px; font-size:0.9em; color: var(--primary-color);"><strong>Conectado como:</strong> <?= htmlspecialchars($_SESSION['rh_user']) ?></div>
    <?php endif; ?>

    <?php if(!empty($error)): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <!-- Scanner de Câmera -->
    <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; margin: 20px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
        <h3 style="text-align:center; color: var(--primary-color); margin-top:0;">📷 Escanear QR Code</h3>
        
        <!-- Aviso sobre HTTPS -->
        <div id="camera-warning" style="display:none; background:#fff3cd; border:1px solid #ffc107; padding:12px; border-radius:8px; margin:10px 0; color:#856404; font-size:0.9em;">
            <strong>⚠️ Atenção:</strong> A câmera pode não funcionar em redes locais. Use a opção "Arquivo de Imagem" para fazer upload de uma foto do QR Code ou acesse via localhost no computador.
        </div>
        
        <div id="reader" style="width:100%; max-width:500px; margin:10px auto; overflow:hidden;"></div>
        <p style="text-align:center; font-size:0.85em; color:#666; margin:10px 0 0 0;">
            📸 Use a câmera ou 📁 faça upload de uma imagem do QR Code
        </p>
    </div>

    <!-- Entrada Manual -->
    <div style="margin: 20px 0;">
        <h3 style="text-align:center; color: var(--primary-color);">⌨️ Ou insira manualmente</h3>
        <form method="POST" id="manual-form">
            <label for="qr_data" style="display:block; text-align:left; font-weight:600; margin-top:12px;">Conteúdo do QR Code</label>
            <input type="text" id="qr_data" name="qr_data" placeholder="Ex: 00000000000:123456" required style="width:100%;">
            <button type="submit" class="btn-primary" style="margin-top:12px; padding:14px;">Verificar Brinde</button>
        </form>
    </div>

    <div class="btn-group" style="margin-top:30px; text-align:center; display:flex; flex-wrap:wrap; justify-content:center; gap:10px;">
        <a href="/" class="theme-btn" style="flex:1; min-width:150px; text-align:center;">Acesso Funcionário</a>
        <a href="/rh_logs.php" class="theme-btn" style="flex:1; min-width:150px; text-align:center;">Ver Logs</a>
        <a href="/rh_funcionarios.php" class="theme-btn" style="flex:1; min-width:150px; text-align:center;">Verificar Funcionários</a>
        <a href="/rh_logout.php" class="theme-btn" style="flex:1; min-width:150px; background:#dc3545; text-align:center;">Sair da Conta</a>
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

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        // Espera o script carregar completamente
        document.addEventListener('DOMContentLoaded', function() {
            function onScanSuccess(decodedText, decodedResult) {
                document.getElementById('qr_data').value = decodedText;
                html5QrcodeScanner.clear().then(() => {
                    document.getElementById('manual-form').submit();
                }).catch(err => {
                    document.getElementById('manual-form').submit();
                });
            }
            
            function onScanFailure(error) {
                // Ignora erros de scan
            }
            
            const html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { 
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0,
                    rememberLastUsedCamera: true,
                    // Traduções para português
                    formatsToSupport: [ Html5QrcodeScanType.SCAN_TYPE_CAMERA, Html5QrcodeScanType.SCAN_TYPE_FILE ],
                    showTorchButtonIfSupported: true,
                    experimentalFeatures: {
                        useBarCodeDetectorIfSupported: true
                    }
                },
                false
            );
            
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            
            // Detecta se está em contexto não seguro e mostra aviso
            if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost') {
                const warning = document.getElementById('camera-warning');
                if(warning) warning.style.display = 'block';
            }
            
            // Traduções não destrutivas: apenas altera texto de nós sem filhos
            function traduzir(){
                const safeSet = (el, txt) => { if(el && el.children.length === 0) el.textContent = txt; };
                const fileBtn = document.getElementById('html5-qrcode-button-file-selection'); safeSet(fileBtn, '📁 Arquivo de Imagem');
                const camStart = document.getElementById('html5-qrcode-button-camera-start'); safeSet(camStart, '📷 Iniciar Scanner');
                const camStop = document.getElementById('html5-qrcode-button-camera-stop'); safeSet(camStop, '⏹ Parar Scanner');
                document.querySelectorAll('#reader label').forEach(l=>{ if(/camera/i.test(l.textContent) && l.children.length===0) l.textContent='Selecione a câmera'; });
                document.querySelectorAll('#reader button').forEach(b=>{ if(/Choose Image/i.test(b.textContent)) safeSet(b,'Escolher Imagem'); });
                document.querySelectorAll('#reader *').forEach(el=>{
                    if(el.children.length===0){
                        if(/Scan an Image File/i.test(el.textContent)) el.textContent='Escanear um Arquivo de Imagem';
                        if(/Or drop an image/i.test(el.textContent)) el.textContent='Ou arraste uma imagem aqui';
                    }
                });
            }
            // Executa após montagem e novamente em pequenos intervalos iniciais para capturar criação tardia
            setTimeout(traduzir, 800);
            let tries = 0; const iv = setInterval(()=>{ traduzir(); if(++tries>5) clearInterval(iv); }, 1000);
        });
    </script>
    <?php
}

include __DIR__ . '/base.php';
