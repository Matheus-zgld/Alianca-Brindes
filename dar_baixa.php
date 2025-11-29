<?php
require_once __DIR__ . '/inc/functions.php';

// Verifica autenticação
if(empty($_SESSION['rh_user'])) {
    header('Location: rh_login.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /rh.php'); exit;
}

$cpf = isset($_POST['cpf']) ? preg_replace('/\D/', '', $_POST['cpf']) : '';
$matricula = isset($_POST['matricula']) ? preg_replace('/\D/', '', $_POST['matricula']) : '';
$matricula = str_pad(substr($matricula, -6), 6, '0', STR_PAD_LEFT);
$nome = isset($_POST['nome']) ? $_POST['nome'] : '';

$pdo = get_db();
$stmt = $pdo->prepare('SELECT * FROM funcionarios WHERE cpf = ? AND matricula = ?');
$stmt->execute([$cpf, $matricula]);
$func = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$func) {
    $error = 'Funcionário não encontrado para dar baixa.';
    $resgatados = [];
    include __DIR__ . '/templates/rh_home.php';
    exit;
}

$data_hora = date('Y-m-d H:i:s');
$update = $pdo->prepare('UPDATE funcionarios SET brinde_status = 1, data_resgate = ? WHERE cpf = ? AND matricula = ? AND brinde_status = 0');
$update->execute([$data_hora, $cpf, $matricula]);

try { log_event('DAR_BAIXA', $cpf, $matricula, $func['nome_completo'], 'Baixa confirmada por RH: ' . ($_SESSION['rh_user'] ?? '-')); } catch(Exception $e) {}

$nome = $nome ?: $func['nome_completo'];
include __DIR__ . '/templates/rh_confirmacao.php';
