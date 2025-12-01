<?php
function render_content(){
    global $error;
    ?>
    <h2>🧑‍💼 Área do Funcionário</h2>
    <p style="text-align:center;">Preencha seus dados para gerar o QR Code de resgate.</p>
    <?php if(!empty($error)): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <form method="POST" style="text-align:center; max-width:500px; margin:0 auto;">
        <div style="text-align:center; margin-bottom:20px;">
            <input type="radio" id="id_cpf" name="identifier" value="cpf" checked style="display:none">
            <input type="radio" id="id_mat" name="identifier" value="matricula" style="display:none">
            <div style="display:inline-block; border-radius:8px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.15); background: #fff;">
                <label for="id_cpf" class="toggle-btn" style="padding:12px 30px; cursor:pointer; display:inline-block; background:#000080; color:#FFD700; font-weight:600; border:none; margin:0; transition:all 0.15s; text-align:center;">CPF</label>
                <label for="id_mat" class="toggle-btn" style="padding:12px 30px; cursor:pointer; display:inline-block; background:#ffffff; color:#000080; font-weight:600; border:none; margin:0; transition:all 0.15s; text-align:center;">Matrícula</label>
            </div>
        </div>

        <!-- Campo 'Nome' removido a pedido: manter apenas CPF e Matrícula -->

        <div id="cpf-row" style="display: block;">
            <label for="cpf" style="display:block; text-align:left; margin-top:12px; font-weight:600;">CPF</label>
            <input type="text" id="cpf" name="cpf" inputmode="numeric" maxlength="14" placeholder="000.000.000-00" title="Informe o CPF">
        </div>

        <div id="mat-row" style="display: none;">
            <label for="matricula" style="display:block; text-align:left; margin-top:12px; font-weight:600;">Matrícula</label>
            <input type="text" id="matricula" name="matricula" placeholder="Digite sua matrícula">
            <div id="mat-help" style="display:none; margin-top:8px; color:#6b6b6b; font-size:13px; text-align:left;">Atenção: a matrícula começa com <strong>0</strong>. Inclua o zero no início do número (ex.: <em>012345</em>).</div>
        </div>

        <button type="submit" class="btn-primary" style="width:100%; margin-top:20px; padding:14px; text-align:center; font-size:16px;">Consultar/Gerar QR Code</button>
    </form>

    <div class="btn-group" style="margin-top:25px; text-align:center;">
        <a href="/rh_login.php" class="theme-btn" style="display:inline-block; min-width:200px; text-align:center; padding:12px 24px;">Acesso RH</a>
    </div>

    <style>
        .toggle-btn { transition: all .15s ease; }
        .toggle-btn.active { box-shadow: inset 0 -3px 0 rgba(0,0,0,0.08); }
    </style>

    <script>
        const btnCpf = document.querySelector('label[for="id_cpf"]');
        const btnMat = document.querySelector('label[for="id_mat"]');
        const radioCpf = document.getElementById('id_cpf');
        const radioMat = document.getElementById('id_mat');
        const cpfRow = document.getElementById('cpf-row');
        const matRow = document.getElementById('mat-row');
        const cpfInput = document.getElementById('cpf');
        const matInput = document.getElementById('matricula');
        const matHelp = document.getElementById('mat-help');

        function setActive(use){
            if(use === 'cpf'){
                btnCpf.classList.add('active');
                btnCpf.style.background = '#000080'; btnCpf.style.color = '#FFD700';
                btnMat.classList.remove('active'); btnMat.style.background = '#ffffff'; btnMat.style.color = '#000080';
                cpfRow.style.display = 'block'; matRow.style.display = 'none';
                cpfInput.required = true; matInput.required = false;
                if(matHelp) matHelp.style.display = 'none';
            } else {
                btnMat.classList.add('active');
                btnMat.style.background = '#000080'; btnMat.style.color = '#FFD700';
                btnCpf.classList.remove('active'); btnCpf.style.background = '#ffffff'; btnCpf.style.color = '#000080';
                cpfRow.style.display = 'none'; matRow.style.display = 'block';
                cpfInput.required = false; matInput.required = true;
                if(matHelp) matHelp.style.display = 'block';
            }
        }

        btnCpf.addEventListener('click', ()=>{ radioCpf.checked = true; setActive('cpf'); });
        btnMat.addEventListener('click', ()=>{ radioMat.checked = true; setActive('matricula'); });
        setActive('cpf');

        cpfInput.addEventListener('input', function(e){
            let v = this.value.replace(/\D/g,'');
            if(v.length > 11) v = v.slice(0,11);
            v = v.replace(/(\d{3})(\d)/,'$1.$2');
            v = v.replace(/(\d{3})(\d)/,'$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/,'$1-$2');
            this.value = v;
        });
    </script>
    <?php
}

include __DIR__ . '/base.php';
