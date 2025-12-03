<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Realizado</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-color: <?= BG_COLOR ?>; /* fallback */
            background-image: url('./imgs/fundo.png');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .container {
            background: white;
            color: #333;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            max-width: 500px;
            text-align: center;
        }
        h1 {
            color: <?= BG_COLOR ?>;
            margin-bottom: 20px;
            font-size: 2em;
        }
        p {
            margin: 15px 0;
            line-height: 1.6;
            color: #666;
        }
        .success-icon {
            font-size: 4em;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            background: <?= BG_COLOR ?>;
            color: <?= FG_COLOR ?>;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            margin-top: 20px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        .note {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 0.9em;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✅</div>
        <h1>Logout Realizado com Sucesso!</h1>
        <p>Você saiu da área do RH.</p>
        <p><strong>Para acessar novamente, será necessário inserir suas credenciais.</strong></p>
        
        <div class="note">
            <strong>💡 Dica:</strong> Se o navegador não pedir credenciais novamente, feche completamente esta aba e abra uma nova.
        </div>
        
        <a href="/index.php" class="btn">Voltar para Área de Funcionários</a>
    </div>
</body>
</html>
