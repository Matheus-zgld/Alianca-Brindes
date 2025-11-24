<?php
require_once __DIR__ . '/inc/functions.php';
rh_authenticate();

$bg_color = '#000080'; $fg_color = '#FFD700'; $logo_url = '/imgs/logo.png';

$pdo = get_db();
$stmt = $pdo->query('SELECT nome_completo, matricula, cpf, data_resgate FROM funcionarios WHERE brinde_status = 1 ORDER BY data_resgate DESC');
$resgatados = $stmt->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qr_data = isset($_POST['qr_data']) ? trim($_POST['qr_data']) : '';
    $parsed = parse_qr_payload($qr_data);
    if(!$parsed) {
        $error = 'QR Code inválido ou não reconhecido. Certifique-se de escanear o código correto.';
        include __DIR__ . '/templates/rh_home.php';
        exit;
    }
    list($cpf, $matricula) = $parsed;
    $stmt = $pdo->prepare('SELECT * FROM funcionarios WHERE cpf = ? AND matricula = ?');
    $stmt->execute([$cpf, $matricula]);
    $func = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$func) {
        $error = 'Funcionário não encontrado. Verifique a Matrícula e o CPF.';
        include __DIR__ . '/templates/rh_home.php';
        exit;
    }
    $funcionario = $func;
    if(intval($func['brinde_status']) === 1) {
        $resgatado = true;
    } else {
        $resgatado = false;
    }
    include __DIR__ . '/templates/rh_status.php';
    exit;
}

include __DIR__ . '/templates/rh_home.php';
