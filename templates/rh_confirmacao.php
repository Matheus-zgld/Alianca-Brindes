<?php
function render_content(){
    global $nome;
    ?>
    <h2 style="color:#080;">✅ Baixa Concluída!</h2>
    <p style="text-align:center; margin:16px 0;">O brinde de <strong><?= htmlspecialchars($nome) ?></strong> foi entregue e o registro foi atualizado para <strong style="color:#080;">RESGATADO</strong> com sucesso.</p>

    <div style="background:#e6ffe6; padding:16px; border-radius:6px; margin:16px 0; text-align:center;">
        <p style="color:#080; font-weight:700; margin:0;">✓ Operação realizada com sucesso!</p>
    </div>

    <div class="btn-group" style="margin-top:20px;"><a href="/rh.php" class="theme-btn">Voltar para Leitura</a></div>
    <?php
}

include __DIR__ . '/base.php';
