<?php
// Variáveis esperadas: $bg_color, $fg_color, $logo_url
require_once __DIR__ . '/../config.php';
if(!isset($bg_color)) $bg_color = BG_COLOR;
if(!isset($fg_color)) $fg_color = FG_COLOR;
if(!isset($logo_url)) $logo_url = LOGO_URL;
// URL da imagem de fundo
$bg_image_url = defined('BG_IMAGE_URL') ? BG_IMAGE_URL : (BASE_URL . '/imgs/fundo.png');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resgate de Brinde de Fim de Ano</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUa6mY5Y2m0hK3sKk3Y5Q5j5i6Q5Y5Z6I8G7bQ5n6m7s8J9k0v1w2x3y4z5a" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary-color: <?= $bg_color ?>; --accent: <?= $fg_color ?>; --surface: #ffffff; --muted:#6b6b6b; --text:#121212; }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Arial; margin:0; padding:0; color:var(--surface); min-height:100vh; display:flex; flex-direction:column; align-items:center; background-color: var(--primary-color); background-image: url('<?= htmlspecialchars($bg_image_url) ?>'); background-size: cover; background-position: center center; background-repeat: no-repeat; background-attachment: fixed; }
        .logo-wrapper { text-align:center; padding:18px; width:100%; }
        .logo-wrapper img { max-height:86px; display:inline-block; margin:0 auto; }
        .container { background-color: var(--surface); color: var(--text); padding:30px; border-radius:12px; box-shadow:0 8px 30px rgba(2,6,23,0.08); width:95%; max-width:980px; margin:18px auto; margin-bottom:28px; }
        h1,h2,h3 { color:var(--primary-color); text-align:center; margin:8px 0; }
        p { text-align:center; margin:8px 0; color:var(--muted); }
        input[type=text], input[type=date], select, textarea { padding:12px 16px; margin:8px 0; border:1px solid #e6e9ee; border-radius:10px; font-size:15px; background:var(--surface); color:var(--text); width:100%; box-sizing:border-box; transition: border-color 0.2s ease; }
        input[type=text]:focus, input[type=date]:focus, select:focus, textarea:focus { outline:none; border-color:var(--primary-color); box-shadow: 0 0 0 3px rgba(0,0,128,0.1); }
        input[type=submit], button { padding:12px 16px; margin:6px 4px; border:none; border-radius:10px; font-size:15px; cursor:pointer; transition:transform .08s ease, box-shadow .12s ease; display:inline-flex; align-items:center; justify-content:center; text-align:center; }
        .btn-primary { background-color: var(--primary-color); color: var(--accent); font-weight:700; box-shadow:0 6px 18px rgba(2,6,23,0.08); width:100%; box-sizing:border-box; display:flex; align-items:center; justify-content:center; }
        .btn-primary:active { transform: translateY(1px); }
        .theme-btn { background:var(--primary-color); color:var(--accent); padding:12px 20px; border-radius:10px; border:none; cursor:pointer; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; text-align:center; margin:4px; box-shadow:0 10px 26px rgba(2,6,23,0.06); transition: all 0.2s ease; box-sizing:border-box; }
        .theme-btn:hover { transform: translateY(-2px); box-shadow:0 12px 30px rgba(2,6,23,0.12); }
        .error { color:#b00020; font-weight:700; text-align:center; padding:10px; background:#fff0f0; border-radius:6px; margin:10px 0; }
        .success { color:#0b6b2e; font-weight:700; text-align:center; padding:10px; background:#f0fff4; border-radius:6px; margin:10px 0; }
        .qr-box { text-align:center; padding:20px; border-radius:10px; margin-top:18px; background:var(--surface); box-shadow:0 8px 26px rgba(2,6,23,0.04); }
        img.qr-code { max-width:100%; height:auto; display:block; margin:12px auto; }
        .table-wrap { overflow:hidden; margin:16px 0; background:var(--surface); border-radius:10px; box-shadow:0 6px 18px rgba(2,6,23,0.04); }
        .responsive-table { width:100%; border-collapse:collapse; background:var(--surface); color:var(--text); font-size:0.95em; min-width:600px; }
        .responsive-table thead { background-color: rgba(0,0,0,0.04); color:var(--muted); }
        .responsive-table th, .responsive-table td { padding:12px 10px; text-align:left; border-bottom:1px solid #f1f4f7; }
        .responsive-table tbody tr:hover { background-color:#fbfdff; }

        /* Badges de Status */
        .badge-resgatado { display:inline-block; padding:6px 12px; border-radius:20px; background:#d4edda; color:#155724; font-weight:600; font-size:0.9em; }
        .badge-pendente { display:inline-block; padding:6px 12px; border-radius:20px; background:#f8d7da; color:#721c24; font-weight:600; font-size:0.9em; }
        .status-resgatado { border-left:4px solid #28a745; }
        .status-pendente { border-left:4px solid #dc3545; }

        /* Mobile: transform table into stacked cards */
        @media (max-width:768px) {
            .container{ padding:16px; width:96%; }
            .btn-group{ display:flex; flex-direction:column; gap:10px; align-items:center; }
            .theme-btn, input[type=submit], button{ width:100%; max-width:100%; text-align:center; }
            .toggle-btn { padding:10px 20px !important; font-size:14px; }
            
            /* Scanner QR responsivo mobile */
            #reader { max-width: 100% !important; width: 100% !important; }
            #reader > div, #reader video, #reader canvas { max-width: 100% !important; width: 100% !important; }
            
            .responsive-table thead { display:none; }
            .responsive-table, .responsive-table tbody, .responsive-table tr, .responsive-table td { display:block; width:100%; }
            .responsive-table tr { margin-bottom:15px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.08); background:var(--surface); padding:15px; position:relative; }
            .responsive-table tr.status-resgatado { border-left:5px solid #28a745; background:#f8fff9; }
            .responsive-table tr.status-pendente { border-left:5px solid #dc3545; background:#fff8f8; }
            .responsive-table td { padding:8px 0; border-bottom:none; }
            .responsive-table td::before { content: attr(data-label) ': '; font-weight:700; color:var(--muted); display:inline-block; min-width:100px; }
            .badge-resgatado, .badge-pendente { display:inline-block; margin-top:5px; }
        }

        @media (max-width:480px) {
            .container{ padding:12px; border-radius:8px; }
            h2{ font-size:1.05em; }
        }

        .form-inline { display:flex; justify-content:center; flex-wrap:wrap; gap:8px; margin:12px 0; }
        .form-inline input, .form-inline select, .form-inline button { flex:1; min-width:120px; }
        
        /* Desktop: largura otimizada para forms */
        @media (min-width:769px) {
            .container { padding:35px 45px; }
            input[type=text], input[type=date], select, textarea { font-size:16px; }
            .btn-primary { font-size:16px; padding:14px 20px; }
        }
        
        @media (max-width:768px) { 
            .form-inline { flex-direction:column; } }
    </style>
</head>
<body>
    <?php if(!empty($logo_url)): ?>
    <div class="logo-wrapper"><img src="<?= htmlspecialchars($logo_url) ?>" alt="Logo"></div>
    <?php endif; ?>
    <div class="container">
        <!-- content -->
        <?php if(function_exists('render_content')) { render_content(); } ?>
    </div>
    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoYz1QZ6q6s9e2g1n6v1xj0y2z3a4b5c6d7e8f9g0h1i2j3" crossorigin="anonymous"></script>
</body>
</html>
