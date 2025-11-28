<?php
/**
 * Arquivo de Configuração do Sistema de Brindes
 * 
 * Configure este arquivo de acordo com o ambiente de produção
 */

// ========== CONFIGURAÇÕES DE AMBIENTE ==========

// Define se estamos em ambiente de desenvolvimento ou produção
define('ENVIRONMENT', 'production'); // 'development' ou 'production'

// ========== CONFIGURAÇÕES DE CAMINHO ==========

// Caminho base da aplicação (diretório raiz)
define('BASE_PATH', __DIR__);

// URL base do site (sem barra final)
// Em produção: http://brindes.alianca.ind.br
// Em desenvolvimento localhost: http://localhost:8000
// Em desenvolvimento rede local: http://192.168.0.26:8000
define('BASE_URL', 'http://brindes.alianca.ind.br');

// ========== CONFIGURAÇÕES DE BANCO DE DADOS ==========

// Caminho do arquivo SQLite
define('DB_PATH', BASE_PATH . '/brindes.db');

// Caminho do arquivo de log
define('LOG_FILE', BASE_PATH . '/data_log.csv');

// ========== CONFIGURAÇÕES DE SEGURANÇA ==========

// Sessão segura (recomendado em produção)
if (ENVIRONMENT === 'production') {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0); // Mude para 1 se usar HTTPS
    ini_set('session.use_strict_mode', 1);
}

// ========== CONFIGURAÇÕES VISUAIS ==========

// Cores do tema
define('BG_COLOR', '#000080'); // Azul escuro
define('FG_COLOR', '#FFD700'); // Amarelo ouro

// Caminho do logo
define('LOGO_URL', BASE_URL . '/imgs/logo.png');

// ========== CONFIGURAÇÕES DE USUÁRIOS RH ==========

// Usuários autorizados para área do RH
// Formato: 'usuario' => 'senha'
$RH_USERS = [
    'rhadmin' => 'rhadmin1927',
    'jose.neto' => 'alianca1927',
    'sara.guimaraes' => 'alianca1927',
    'patricia.simoes' => 'alianca1927',
    'liberato.silva' => 'alianca1927'
];

// ========== CONFIGURAÇÕES DE QR CODE ==========

// Tamanho padrão do QR Code em pixels
define('QR_SIZE', 300);

// Timeout para requisições externas (Google Charts API)
define('QR_TIMEOUT', 5);

// ========== CONFIGURAÇÕES DE LOG ==========

// Ativar/desativar logging
define('ENABLE_LOGGING', true);

// ========== EXIBIÇÃO DE ERROS ==========

if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/php_errors.log');
}

// ========== TIMEZONE ==========

date_default_timezone_set('America/Sao_Paulo');

?>
