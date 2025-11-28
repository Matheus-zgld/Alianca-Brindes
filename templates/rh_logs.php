<?php
function render_content(){
    global $q, $action_filter, $date_from, $date_to;
    // Ler CSV de logs
    $qr_logs = [];
    $baixa_logs = [];
    $logfile = __DIR__ . '/../data_log.csv';
    if(file_exists($logfile)){
        if(($fh = fopen($logfile, 'r')) !== false){
            $hdr = fgetcsv($fh);
            while(($row = fgetcsv($fh)) !== false){
                $entry = array_combine($hdr, $row);
                $action = $entry['action'] ?? '';
                $ts = $entry['timestamp'] ?? '';
                $parts = explode(' ', $ts);
                $date_part = $parts[0] ?? '';
                $time_part = $parts[1] ?? '';
                $entry2 = ['timestamp'=>$ts,'date'=>$date_part,'time'=>$time_part,'action'=>$action,'cpf'=>$entry['cpf'] ?? '','matricula'=>$entry['matricula'] ?? '','nome'=>$entry['nome'] ?? '','remote_addr'=>$entry['remote_addr'] ?? '','user_agent'=>$entry['user_agent'] ?? '','extra'=>$entry['extra'] ?? ''];
                // Formata data para DD-MM-YY para exibição
                if(!empty($entry2['date'])){
                    $dtObj = DateTime::createFromFormat('Y-m-d', $entry2['date']);
                    if($dtObj){
                        $entry2['display_date'] = $dtObj->format('d-m-y');
                    } else {
                        // Tenta outros formatos se necessário
                        $entry2['display_date'] = $entry2['date'];
                    }
                } else {
                    $entry2['display_date'] = '';
                }
                if($action_filter && $action_filter != $action) continue;
                if(!empty($date_from) && $entry2['date'] && $entry2['date'] < $date_from) continue;
                if(!empty($date_to) && $entry2['date'] && $entry2['date'] > $date_to) continue;
                if(!empty($q)){
                    $hay = strtolower(($entry2['cpf'] ?? '') . ' ' . ($entry2['matricula'] ?? '') . ' ' . ($entry2['nome'] ?? '') . ' ' . ($entry2['extra'] ?? '') . ' ' . ($entry2['user_agent'] ?? ''));
                    if(stripos($hay, strtolower($q)) === false) continue;
                }
                if($action === 'QR_GENERATED') $qr_logs[] = $entry2; elseif($action === 'DAR_BAIXA') $baixa_logs[] = $entry2;
            }
            fclose($fh);
        }
    }
    ?>
    <h2>📜 Logs do Sistema</h2>
    <p style="text-align:center;">Visão completa dos eventos: geração de QR e baixas realizadas pelo RH.</p>

    <form method="GET" class="form-inline">
        <input type="text" name="q" value="<?= htmlspecialchars($q ?? '') ?>" placeholder="Pesquisar (nome / CPF / matrícula)">
        <select name="action">
            <option value="">Todas Ações</option>
            <option value="QR_GENERATED" <?= (!empty($action_filter) && $action_filter==='QR_GENERATED') ? 'selected' : '' ?>>Geração de QR</option>
            <option value="DAR_BAIXA" <?= (!empty($action_filter) && $action_filter==='DAR_BAIXA') ? 'selected' : '' ?>>Dar Baixa</option>
        </select>
        <input type="date" name="date_from" value="<?= htmlspecialchars($date_from ?? '') ?>">
        <input type="date" name="date_to" value="<?= htmlspecialchars($date_to ?? '') ?>">
        <button type="submit" class="btn-primary">Pesquisar</button>
    </form>

    <div class="btn-group" style="margin-top:20px;"><a href="/rh.php" class="theme-btn">Voltar</a></div>

    <div style="margin-top:16px;"><h3 style="color:var(--primary-color);">Logs de Geração de QR Code</h3>
    <div class="table-wrap">
    <?php if($qr_logs): ?>
    <table class="responsive-table"><thead><tr><th>Data</th><th>Hora</th><th>CPF</th><th>Matrícula</th><th>Nome</th><th>IP</th><th>User-Agent</th><th>Extra</th></tr></thead><tbody>
    <?php foreach(array_reverse($qr_logs) as $r): ?>
        <tr>
            <td data-label="Data"><?= htmlspecialchars($r['display_date']) ?></td>
            <td data-label="Hora"><?= htmlspecialchars($r['time']) ?></td>
            <td data-label="CPF"><?= htmlspecialchars($r['cpf']) ?></td>
            <td data-label="Matrícula"><?= htmlspecialchars($r['matricula']) ?></td>
            <td data-label="Nome"><?= htmlspecialchars($r['nome']) ?></td>
            <td data-label="IP"><?= htmlspecialchars($r['remote_addr']) ?></td>
            <td data-label="User-Agent"><?= htmlspecialchars(substr($r['user_agent'],0,40)) ?>...</td>
            <td data-label="Extra"><?= htmlspecialchars(substr($r['extra'],0,30)) ?>...</td>
        </tr>
    <?php endforeach; ?></tbody></table>
    <?php else: ?><p style="text-align:center; padding:16px;">Nenhum log de QR gerado encontrado.</p><?php endif; ?>
    </div></div>

    <div style="margin-top:20px;"><h3 style="color:var(--primary-color);">Logs de Dar Baixa</h3>
    <div class="table-wrap">
    <?php if($baixa_logs): ?>
    <table class="responsive-table"><thead><tr><th>Data</th><th>Hora</th><th>CPF</th><th>Matrícula</th><th>Nome</th><th>IP</th><th>User-Agent</th><th>RH</th></tr></thead><tbody>
    <?php foreach(array_reverse($baixa_logs) as $r): ?>
        <tr>
            <td data-label="Data"><?= htmlspecialchars($r['display_date']) ?></td>
            <td data-label="Hora"><?= htmlspecialchars($r['time']) ?></td>
            <td data-label="CPF"><?= htmlspecialchars($r['cpf']) ?></td>
            <td data-label="Matrícula"><?= htmlspecialchars($r['matricula']) ?></td>
            <td data-label="Nome"><?= htmlspecialchars($r['nome']) ?></td>
            <td data-label="IP"><?= htmlspecialchars($r['remote_addr']) ?></td>
            <td data-label="User-Agent"><?= htmlspecialchars(substr($r['user_agent'],0,40)) ?>...</td>
            <td data-label="RH"><?= htmlspecialchars($r['extra'] ? explode(':', $r['extra'])[1] ?? '-' : '-') ?></td>
        </tr>
    <?php endforeach; ?></tbody></table>
    <?php else: ?><p style="text-align:center; padding:16px;">Nenhum log de baixa encontrado.</p><?php endif; ?>
    </div></div>

    <?php
}

include __DIR__ . '/base.php';
