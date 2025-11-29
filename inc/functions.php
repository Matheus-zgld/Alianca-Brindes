<?php
// Funções utilitárias para a aplicação em PHP

// Carrega arquivo de configuração
require_once __DIR__ . '/../config.php';

// Inicia sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function get_db() {
    $dsn = 'sqlite:' . DB_PATH;
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

function log_event($action, $cpf = '', $matricula = '', $nome = '', $extra = '') {
    $header = ['timestamp','action','cpf','matricula','nome','remote_addr','user_agent','extra'];
    $ts = date('Y-m-d H:i:s');
    $remote = '';
    if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $remote = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif(!empty($_SERVER['REMOTE_ADDR'])) {
        $remote = $_SERVER['REMOTE_ADDR'];
    }
    $ua = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

    $write_header = !file_exists(LOG_FILE);
    $fh = fopen(LOG_FILE, 'a');
    if($write_header) {
        fputcsv($fh, $header);
    }
    fputcsv($fh, [$ts,$action,$cpf,$matricula,$nome,$remote,$ua,$extra]);
    fclose($fh);
}

function generate_qr_url($data, $size=300) {
    // Tenta gerar QR como data URI buscando a imagem do Google Chart e retornando base64.
    // Se falhar (ex: allow_url_fopen desabilitado), retorna a URL externa como fallback.
    $chart_url = 'https://chart.googleapis.com/chart?chs=' . intval($size) . 'x' . intval($size) . '&cht=qr&chl=' . urlencode($data) . '&choe=UTF-8';
    // tentar file_get_contents primeiro
    $img = false;
    if(function_exists('file_get_contents')){
        $context = stream_context_create(['http' => ['timeout' => 5]]);
        $img = @file_get_contents($chart_url, false, $context);
    }
    // se falhou, tentar cURL
    if($img === false && function_exists('curl_init')){
        $ch = curl_init($chart_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $img = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($img === false || $code >= 400) $img = false;
    }
    if($img !== false && strlen($img) > 0){
        $b64 = base64_encode($img);
        return 'data:image/png;base64,' . $b64;
    }
    return $chart_url;
}

// Build a normalized QR payload from cpf and matricula.
// Ensures CPF has only digits (11) and matricula is zero-padded to 6 digits.
function build_qr_payload($cpf, $matricula) {
    $cpf_digits = preg_replace('/\D/', '', (string)$cpf);
    $mat_digits = preg_replace('/\D/', '', (string)$matricula);
    if(strlen($cpf_digits) < 11) {
        // cannot build properly
        return false;
    }
    $cpf_digits = str_pad(substr($cpf_digits, -11), 11, '0', STR_PAD_LEFT);
    $mat_digits = str_pad(substr($mat_digits, -6), 6, '0', STR_PAD_LEFT);
    return $cpf_digits . ':' . $mat_digits;
}

// Parse a scanned QR payload trying several common formats and return [cpf, matricula] or false.
function parse_qr_payload($payload) {
    if(empty($payload)) return false;
    $s = trim((string)$payload);
    // If it's a URL, try to extract query param 'q' or 'qr' or last path segment
    if(preg_match('#https?://#i', $s)) {
        $parts = parse_url($s);
        if(!empty($parts['query'])) {
            parse_str($parts['query'], $qs);
            foreach(['q','qr','data','payload'] as $k) {
                if(!empty($qs[$k])) {
                    $s = $qs[$k]; break;
                }
            }
        } elseif(!empty($parts['path'])) {
            $segments = explode('/', trim($parts['path'], '/'));
            $last = end($segments);
            if($last) $s = $last;
        }
    }

    // Remove surrounding whitespace and common wrappers
    $s = trim($s);

    // If contains colon, split and sanitize parts
    if(strpos($s, ':') !== false) {
        list($a, $b) = array_map(function($x){ return preg_replace('/\D/', '', $x); }, explode(':', $s, 2));
        if(strlen($a) == 11 && strlen($b) <= 6) {
            $b = str_pad(substr($b, -6), 6, '0', STR_PAD_LEFT);
            return [$a, $b];
        }
        // maybe reversed
        if(strlen($b) == 11 && strlen($a) <= 6) {
            $a = str_pad(substr($a, -6), 6, '0', STR_PAD_LEFT);
            return [$b, $a];
        }
    }

    // If it's just digits, try to find 11+6 sequences
    $digits = preg_replace('/\D/', '', $s);
    if(preg_match('/(\d{11})(\d{6})/', $digits, $m)) {
        return [$m[1], $m[2]];
    }
    if(preg_match('/(\d{6})(\d{11})/', $digits, $m)) {
        return [$m[2], $m[1]];
    }

    // Last attempt: find any 11-digit and any 6-digit somewhere
    if(preg_match('/(\d{11})/', $digits, $m1) && preg_match('/(\d{6})/', $digits, $m2)) {
        return [$m1[1], $m2[1]];
    }

    return false;
}

function validate_cpf($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', (string)$cpf);
    if(strlen($cpf) != 11) return false;
    if(preg_match('/^(\d)\1{10}$/', $cpf)) return false;

    $calc = function($digits) {
        $sum = 0; $len = strlen($digits);
        for($i=0; $i<$len; $i++) {
            $sum += intval($digits[$i]) * ($len + 1 - $i);
        }
        $r = $sum % 11;
        return ($r < 2) ? 0 : 11 - $r;
    };

    $d1 = $calc(substr($cpf,0,9));
    $d2 = $calc(substr($cpf,0,10));
    return ($d1 == intval($cpf[9]) && $d2 == intval($cpf[10]));
}

function rh_authenticate() {
    // Verifica se usuário está autenticado na sessão
    if(!empty($_SESSION['rh_user'])) {
        return true;
    }
    
    // Não autenticado: redireciona para login
    header('Location: rh_login.php');
    exit;
}

?>
