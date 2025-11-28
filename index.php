<?php
require_once __DIR__ . '/inc/functions.php';

$bg_color = BG_COLOR;
$fg_color = FG_COLOR;
$logo_url = LOGO_URL;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = isset($_POST['identifier']) ? $_POST['identifier'] : 'cpf';
    $cpf = isset($_POST['cpf']) ? trim($_POST['cpf']) : '';
    $matricula = isset($_POST['matricula']) ? trim($_POST['matricula']) : '';

    $pdo = get_db();
    try {
        if($identifier === 'cpf') {
            // Normaliza o CPF (remove formatação)
            $cpf_clean = preg_replace('/\D/', '', $cpf);
            
            if(!validate_cpf($cpf_clean)) {
                $error = 'CPF inválido. Verifique o número e tente novamente.';
                include __DIR__ . '/templates/funcionario_home.php';
                exit;
            }
            
            // Busca no banco com CPF normalizado
            $stmt = $pdo->prepare('SELECT * FROM funcionarios WHERE cpf = ?');
            $stmt->execute([$cpf_clean]);
            $func = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            if($matricula === '') {
                $error = 'Informe a matrícula.';
                include __DIR__ . '/templates/funcionario_home.php';
                exit;
            }
            $stmt = $pdo->prepare('SELECT * FROM funcionarios WHERE matricula = ?');
            $stmt->execute([$matricula]);
            $func = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        $error = 'Erro interno de DB: ' . $e->getMessage();
        include __DIR__ . '/templates/funcionario_home.php';
        exit;
    }

    if(!$func) {
        $error = 'Funcionário não encontrado no cadastro. Consulte o RH.';
        include __DIR__ . '/templates/funcionario_home.php';
        exit;
    }

    if(intval($func['brinde_status']) === 1) {
        $status = 'RESGATADO';
        $data = date('d-m-y H:i:s', strtotime($func['data_resgate']));
        include __DIR__ . '/templates/status.php';
        exit;
    }

    // Gera QR padronizado e registra log
    $qr_data = build_qr_payload($func['cpf'], $func['matricula']);
    if($qr_data === false) {
        $error = 'Erro ao gerar QR: dados inválidos.';
        include __DIR__ . '/templates/funcionario_home.php';
        exit;
    }
    $qr_url = generate_qr_url($qr_data, 300);
    try { log_event('QR_GENERATED', $func['cpf'], $func['matricula'], $func['nome_completo'], 'QR gerado pelo funcionário'); } catch(Exception) {}

    $nome = $func['nome_completo'];
    $qr_content = $qr_data;
    include __DIR__ . '/templates/qr_code_display.php';
    exit;
}

// GET
include __DIR__ . '/templates/funcionario_home.php';
