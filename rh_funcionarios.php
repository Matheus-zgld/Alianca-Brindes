<?php
require_once __DIR__ . '/inc/functions.php';
rh_authenticate();

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

$pdo = get_db();
$rows = $pdo->query('SELECT nome_completo, cpf, matricula, brinde_status FROM funcionarios ORDER BY nome_completo')->fetchAll(PDO::FETCH_ASSOC);
$funcionarios = [];
foreach($rows as $r){
    $funcionarios[] = ['nome_completo'=>strval($r['nome_completo'] ?? ''),'cpf'=>strval($r['cpf'] ?? ''),'matricula'=>strval($r['matricula'] ?? ''),'brinde_status'=>intval($r['brinde_status'] ?? 0)];
}
if($q){
    $ql = strtolower($q);
    $funcionarios = array_filter($funcionarios, function($f) use($ql){ return strpos(strtolower($f['nome_completo'].' '.$f['cpf'].' '.$f['matricula']), $ql) !== false; });
}
if($status === '0' || $status === '1'){
    $funcionarios = array_filter($funcionarios, function($f) use($status){ return strval($f['brinde_status']) === $status; });
}

// Render simple page
$bg_color = BG_COLOR;
$fg_color = FG_COLOR;
$logo_url = LOGO_URL;
include __DIR__ . '/templates/rh_funcionarios.php';
