<?php
/**
 * Script de Verificação do Sistema
 * 
 * Este script verifica se todos os componentes necessários estão instalados
 * e configurados corretamente para o funcionamento do sistema.
 * 
 * IMPORTANTE: Execute este script e depois EXCLUA-O por segurança!
 */

// Carrega configurações
require_once __DIR__ . '/config.php';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação do Sistema - Brindes Aliança</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            padding: 20px;
            min-height: 100vh;
            background-color: #000; /* fallback */
            background-image: url('http://brindes.alianca.ind.br/imgs/fundo.png');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            padding: 30px;
        }
        h1 {
            color: #000080;
            border-bottom: 3px solid #FFD700;
            padding-bottom: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        h2 {
            color: #000080;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-left: 10px;
            border-left: 4px solid #FFD700;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            padding: 10px;
            margin: 5px 0;
            background: white;
            border-radius: 5px;
            border-left: 4px solid #ddd;
        }
        .ok { border-left-color: #28a745; }
        .warning { border-left-color: #ffc107; }
        .error { border-left-color: #dc3545; }
        .icon {
            font-weight: bold;
            margin-right: 10px;
        }
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            color: #856404;
        }
        .error-box {
            background: #f8d7da;
            border: 1px solid #dc3545;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            color: #721c24;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #000080;
            color: #FFD700;
            font-weight: bold;
        }
        tr:hover { background: #f5f5f5; }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #FFD700;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verificação do Sistema de Brindes</h1>
        
        <?php
        $errors = 0;
        $warnings = 0;
        
        // ========== VERIFICAÇÃO DE EXTENSÕES PHP ==========
        ?>
        <div class="section">
            <h2>📦 Extensões PHP</h2>
            <ul>
                <?php
                $required_extensions = [
                    'pdo_sqlite' => ['required' => true, 'desc' => 'Necessário para o banco de dados'],
                    'mbstring' => ['required' => true, 'desc' => 'Manipulação de strings UTF-8'],
                    'json' => ['required' => true, 'desc' => 'Processamento de dados JSON'],
                    'curl' => ['required' => false, 'desc' => 'Geração de QR codes (alternativa)'],
                    'gd' => ['required' => false, 'desc' => 'Manipulação de imagens'],
                ];
                
                foreach($required_extensions as $ext => $info) {
                    $loaded = extension_loaded($ext);
                    $class = $loaded ? 'ok' : ($info['required'] ? 'error' : 'warning');
                    $icon = $loaded ? '✅' : ($info['required'] ? '❌' : '⚠️');
                    
                    if(!$loaded && $info['required']) $errors++;
                    if(!$loaded && !$info['required']) $warnings++;
                    
                    echo "<li class='$class'>";
                    echo "<span class='icon'>$icon</span>";
                    echo "<strong>$ext:</strong> ";
                    echo $loaded ? 'Instalada' : 'Não instalada';
                    echo " - <em>{$info['desc']}</em>";
                    echo "</li>";
                }
                ?>
            </ul>
        </div>

        <?php
        // ========== VERIFICAÇÃO DE CONFIGURAÇÕES PHP ==========
        ?>
        <div class="section">
            <h2>⚙️ Configurações PHP</h2>
            <table>
                <tr>
                    <th>Configuração</th>
                    <th>Valor Atual</th>
                    <th>Status</th>
                </tr>
                <?php
                $php_configs = [
                    'allow_url_fopen' => ['check' => ini_get('allow_url_fopen'), 'expected' => '1', 'critical' => false],
                    'file_uploads' => ['check' => ini_get('file_uploads'), 'expected' => '1', 'critical' => false],
                    'max_execution_time' => ['check' => ini_get('max_execution_time'), 'expected' => '>=30', 'critical' => false],
                    'memory_limit' => ['check' => ini_get('memory_limit'), 'expected' => '>=128M', 'critical' => false],
                ];
                
                foreach($php_configs as $config => $info) {
                    $value = $info['check'];
                    $ok = true;
                    
                    if(strpos($info['expected'], '>=') === 0) {
                        $expected = intval(substr($info['expected'], 2));
                        $actual = intval($value);
                        $ok = ($actual >= $expected);
                    } else {
                        $ok = ($value == $info['expected']);
                    }
                    
                    $status = $ok ? '✅ OK' : '⚠️ Verificar';
                    if(!$ok && $info['critical']) $errors++;
                    if(!$ok && !$info['critical']) $warnings++;
                    
                    echo "<tr>";
                    echo "<td><strong>$config</strong></td>";
                    echo "<td>$value</td>";
                    echo "<td>$status</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>

        <?php
        // ========== VERIFICAÇÃO DE ARQUIVOS E PERMISSÕES ==========
        ?>
        <div class="section">
            <h2>📁 Arquivos e Permissões</h2>
            <ul>
                <?php
                $files_to_check = [
                    'config.php' => ['writable' => false, 'critical' => true],
                    'brindes.db' => ['writable' => true, 'critical' => true],
                    'data_log.csv' => ['writable' => true, 'critical' => true],
                    '.htaccess' => ['writable' => false, 'critical' => true],
                    'inc/functions.php' => ['writable' => false, 'critical' => true],
                ];
                
                foreach($files_to_check as $file => $info) {
                    $exists = file_exists($file);
                    $writable = is_writable($file);
                    $readable = is_readable($file);
                    
                    $status = 'ok';
                    $message = '';
                    
                    if(!$exists) {
                        $status = 'error';
                        $message = '❌ Arquivo não encontrado';
                        $errors++;
                    } elseif(!$readable) {
                        $status = 'error';
                        $message = '❌ Sem permissão de leitura';
                        $errors++;
                    } elseif($info['writable'] && !$writable) {
                        $status = 'error';
                        $message = '❌ Sem permissão de escrita (necessário)';
                        $errors++;
                    } elseif(!$info['writable'] && $writable) {
                        $status = 'warning';
                        $message = '⚠️ Tem permissão de escrita (desnecessário)';
                        $warnings++;
                    } else {
                        $message = '✅ OK';
                    }
                    
                    echo "<li class='$status'>";
                    echo "<strong>$file:</strong> $message";
                    
                    if($exists) {
                        $perms = substr(sprintf('%o', fileperms($file)), -4);
                        echo " <em>(Permissões: $perms)</em>";
                    }
                    
                    echo "</li>";
                }
                ?>
            </ul>
        </div>

        <?php
        // ========== VERIFICAÇÃO DO BANCO DE DADOS ==========
        ?>
        <div class="section">
            <h2>🗄️ Banco de Dados</h2>
            <?php
            try {
                $pdo = new PDO('sqlite:' . DB_PATH);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                echo "<div class='info-box'>";
                echo "<strong>✅ Conexão estabelecida com sucesso!</strong><br>";
                echo "Arquivo: " . DB_PATH;
                echo "</div>";
                
                // Verifica tabelas
                $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
                
                echo "<strong>Tabelas encontradas:</strong><ul>";
                foreach($tables as $table) {
                    $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
                    echo "<li class='ok'>✅ <strong>$table</strong> - $count registros</li>";
                }
                echo "</ul>";
                
                // Estatísticas específicas
                if(in_array('funcionarios', $tables)) {
                    $total = $pdo->query("SELECT COUNT(*) FROM funcionarios")->fetchColumn();
                    $resgatados = $pdo->query("SELECT COUNT(*) FROM funcionarios WHERE brinde_status = 1")->fetchColumn();
                    $pendentes = $total - $resgatados;
                    
                    echo "<div class='info-box'>";
                    echo "<strong>📊 Estatísticas:</strong><br>";
                    echo "Total de funcionários: <strong>$total</strong><br>";
                    echo "Brindes resgatados: <strong>$resgatados</strong><br>";
                    echo "Brindes pendentes: <strong>$pendentes</strong>";
                    echo "</div>";
                }
                
            } catch(Exception $e) {
                echo "<div class='error-box'>";
                echo "<strong>❌ Erro ao conectar com o banco de dados:</strong><br>";
                echo htmlspecialchars($e->getMessage());
                echo "</div>";
                $errors++;
            }
            ?>
        </div>

        <?php
        // ========== INFORMAÇÕES DO SERVIDOR ==========
        ?>
        <div class="section">
            <h2>🖥️ Informações do Servidor</h2>
            <table>
                <tr>
                    <th>Item</th>
                    <th>Valor</th>
                </tr>
                <tr>
                    <td><strong>Versão do PHP</strong></td>
                    <td><?php echo phpversion(); ?></td>
                </tr>
                <tr>
                    <td><strong>Servidor Web</strong></td>
                    <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido'; ?></td>
                </tr>
                <tr>
                    <td><strong>Document Root</strong></td>
                    <td><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Desconhecido'; ?></td>
                </tr>
                <tr>
                    <td><strong>Diretório do Script</strong></td>
                    <td><?php echo __DIR__; ?></td>
                </tr>
                <tr>
                    <td><strong>Sistema Operacional</strong></td>
                    <td><?php echo PHP_OS; ?></td>
                </tr>
                <tr>
                    <td><strong>Ambiente</strong></td>
                    <td><?php echo ENVIRONMENT; ?></td>
                </tr>
                <tr>
                    <td><strong>URL Base</strong></td>
                    <td><?php echo BASE_URL; ?></td>
                </tr>
            </table>
        </div>

        <?php
        // ========== TESTE DE GERAÇÃO DE QR CODE ==========
        ?>
        <div class="section">
            <h2>📱 Teste de QR Code</h2>
            <?php
            try {
                require_once __DIR__ . '/inc/functions.php';
                $test_qr = build_qr_payload('12345678901', '123456');
                $qr_url = generate_qr_url($test_qr, 200);
                
                if($qr_url) {
                    echo "<div class='info-box'>";
                    echo "<strong>✅ Geração de QR Code funcionando!</strong><br>";
                    echo "Teste: <code>$test_qr</code><br><br>";
                    echo "<img src='$qr_url' alt='QR Code de teste' style='max-width: 200px;'>";
                    echo "</div>";
                } else {
                    echo "<div class='warning-box'>";
                    echo "<strong>⚠️ QR Code pode não estar funcionando corretamente</strong>";
                    echo "</div>";
                    $warnings++;
                }
            } catch(Exception $e) {
                echo "<div class='error-box'>";
                echo "<strong>❌ Erro ao gerar QR Code:</strong><br>";
                echo htmlspecialchars($e->getMessage());
                echo "</div>";
                $errors++;
            }
            ?>
        </div>

        <?php
        // ========== RESULTADO FINAL ==========
        ?>
        <div class="section">
            <h2>📋 Resultado Final</h2>
            <?php
            if($errors == 0 && $warnings == 0) {
                echo "<div class='info-box' style='background: #d4edda; border-color: #c3e6cb; color: #155724;'>";
                echo "<h3 style='margin: 0 0 10px 0; color: #155724;'>✅ Sistema pronto para uso!</h3>";
                echo "<p>Todos os componentes estão instalados e configurados corretamente.</p>";
                echo "<p><strong>Próximos passos:</strong></p>";
                echo "<ol style='margin-left: 20px;'>";
                echo "<li>EXCLUA este arquivo (verify.php) por segurança</li>";
                echo "<li>Acesse a página principal: <a href='index.php'>index.php</a></li>";
                echo "<li>Teste a área do RH: <a href='rh.php'>rh.php</a></li>";
                echo "</ol>";
                echo "</div>";
            } elseif($errors == 0) {
                echo "<div class='warning-box'>";
                echo "<h3 style='margin: 0 0 10px 0;'>⚠️ Sistema funcional com avisos</h3>";
                echo "<p>Encontrados <strong>$warnings aviso(s)</strong> que não impedem o funcionamento, mas devem ser revisados.</p>";
                echo "<p><strong>Ação recomendada:</strong> Revisar os avisos acima e corrigir se possível.</p>";
                echo "</div>";
            } else {
                echo "<div class='error-box'>";
                echo "<h3 style='margin: 0 0 10px 0;'>❌ Problemas encontrados</h3>";
                echo "<p>Encontrados <strong>$errors erro(s) crítico(s)</strong> e <strong>$warnings aviso(s)</strong>.</p>";
                echo "<p><strong>Ação necessária:</strong> Corrija os erros marcados com ❌ antes de usar o sistema.</p>";
                echo "</div>";
            }
            ?>
        </div>

        <div class="warning-box">
            <strong>🔒 AVISO DE SEGURANÇA:</strong><br>
            Este arquivo contém informações sensíveis sobre o sistema.<br>
            <strong>EXCLUA este arquivo imediatamente após a verificação!</strong>
        </div>

        <div class="footer">
            <p>Sistema de Brindes - Aliança Industrial</p>
            <p><small>Verificação executada em <?php echo date('d/m/Y \à\s H:i:s'); ?></small></p>
        </div>
    </div>
</body>
</html>
