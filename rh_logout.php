<?php
// Desabilita cache
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

require_once __DIR__ . '/inc/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Registra logout antes de destruir sessão
if(!empty($_SESSION['rh_user'])) {
    try { log_event('RH_LOGOUT', '', '', '', 'Logout RH: ' . $_SESSION['rh_user']); } catch(Exception $e) {}
}

// Destrói completamente a sessão
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();

// Redireciona para área de funcionários
header('Location: /index.php');
exit;
