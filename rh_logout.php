<?php
require_once __DIR__ . '/inc/functions.php';
// Limpa sessão e tenta forçar nova autenticação HTTP Basic
session_start();
session_unset();
session_destroy();

// Envia 401 para forçar o navegador a pedir credenciais novamente.
header('WWW-Authenticate: Basic realm="Login Obrigatório"');
header('HTTP/1.0 401 Unauthorized');
echo "Você saiu. Para acessar a Área RH novamente, insira suas credenciais. <a href=\"/rh.php\">Entrar</a>";
exit;
