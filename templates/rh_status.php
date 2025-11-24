<?php
function render_content(){
    global $funcionario, $resgatado;
    ?>
    <h2>📋 Detalhes do Resgate</h2>

    <?php if($resgatado): ?>
        <h3 style="color:#c00; text-align:center;">⚠️ ALERTA: BRINDE JÁ RESGATADO</h3>
        <p style="text-align:center;">Este funcionário já retirou o brinde em: <strong><?= htmlspecialchars($funcionario['data_resgate']) ?></strong></p>
    <?php else: ?>
        <h3 style="color:#080; text-align:center;">✅ STATUS: PRONTO PARA RESGATE</h3>
    <?php endif; ?>

    <div style="background:#f9f9f9; padding:16px; border-radius:6px; margin:16px 0;">
        <p style="text-align:left; margin:8px 0;"><strong>Nome:</strong> <?= htmlspecialchars($funcionario['nome_completo']) ?></p>
        <p style="text-align:left; margin:8px 0;"><strong>Matrícula:</strong> <?= htmlspecialchars($funcionario['matricula']) ?></p>
        <p style="text-align:left; margin:8px 0;"><strong>CPF:</strong> <?= htmlspecialchars($funcionario['cpf']) ?></p>
    </div>

    <?php if(!$resgatado): ?>
        <form method="POST" action="/dar_baixa.php" style="text-align:center;">
            <input type="hidden" name="cpf" value="<?= htmlspecialchars($funcionario['cpf']) ?>">
            <input type="hidden" name="matricula" value="<?= htmlspecialchars($funcionario['matricula']) ?>">
            <input type="hidden" name="nome" value="<?= htmlspecialchars($funcionario['nome_completo']) ?>">
            <button type="submit" style="background-color:#080; color:#fff; padding:12px 20px; width:100%; font-size:1em; font-weight:700;">CONFIRMAR ENTREGA E DAR BAIXA</button>
        </form>
    <?php else: ?>
        <p style="text-align:center; margin-top:20px; color:#c00; font-weight:700;">Não é possível dar baixa novamente.</p>
    <?php endif; ?>

    <div class="btn-group" style="margin-top:20px;"><a href="/rh.php" class="theme-btn">Voltar</a></div>
    <?php
}

include __DIR__ . '/base.php';
