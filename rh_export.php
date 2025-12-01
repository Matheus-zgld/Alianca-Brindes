<?php
session_start();
if(empty($_SESSION['rh_user'])) { header('Location: rh_login.php'); exit; }

try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/brindes.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // build map of who did the baixa from data_log.csv
    $log_file = __DIR__ . '/data_log.csv';
    $rh_users_map = [];
    if (file_exists($log_file)) {
        $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $cols = str_getcsv($line);
            if (count($cols) >= 8 && strtolower($cols[1]) === 'dar_baixa') {
                $key = $cols[2] . ':' . $cols[3];
                if (preg_match('/RH:\s*(\S+)/', $cols[7], $m)) {
                    $rh_users_map[$key] = $m[1];
                }
            }
        }
    }

    // fetch all funcionarios
    $st = $pdo->query('SELECT * FROM funcionarios');
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    // prepare CSV headers
    if (count($rows) > 0) {
        $fields = array_keys($rows[0]);
    } else {
        $fields = ['nome_completo','cpf','matricula','brinde_status','data_resgate'];
    }
    // appended columns
    // remove 'recebeu' column as requested; keep RH responsible
    $fields[] = 'rh_responsavel';

    // send headers for download
    $filename = 'funcionarios_export_' . date('Y-m-d') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // output BOM for Excel compatibility and hint separator
    $out = fopen('php://output', 'w');
    // UTF-8 BOM
    echo "\xEF\xBB\xBF";
    // Tell Excel to use semicolon as separator (helps Brazilian Excel setups)
    echo "sep=;\r\n";

    // write header using semicolon as delimiter
    fputcsv($out, $fields, ';');

    // write rows (semicolon separated)
    foreach ($rows as $r) {
        $key = (isset($r['cpf']) ? $r['cpf'] : '') . ':' . (isset($r['matricula']) ? $r['matricula'] : '');
        $rh_user = isset($rh_users_map[$key]) ? $rh_users_map[$key] : '';

        // ensure order matches header
        $line = [];
        foreach ($fields as $f) {
            if ($f === 'brinde_status') {
                // map 1 -> resgatado, 0 -> pendente
                $line[] = (isset($r['brinde_status']) && intval($r['brinde_status']) === 1) ? 'resgatado' : 'pendente';
            } elseif ($f === 'rh_responsavel') {
                $line[] = $rh_user;
            } else {
                $line[] = isset($r[$f]) ? $r[$f] : '';
            }
        }
        fputcsv($out, $line, ';');
    }
    fclose($out);
    exit;
} catch (Throwable $e) {
    die('Erro ao gerar exportação: ' . htmlspecialchars($e->getMessage()));
}
