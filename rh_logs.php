<?php
session_start();
if(empty($_SESSION['rh_user'])){header('Location: rh_login.php');exit;}
$q='';$action_filter='';$logs=[];
if(isset($_GET['q']))$q=trim($_GET['q']);
if(isset($_GET['action']))$action_filter=trim($_GET['action']);
$log_file=__DIR__.'/data_log.csv';
if(file_exists($log_file)){
    $lines=file($log_file,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
    foreach($lines as $line){
        $cols=str_getcsv($line);
        if(count($cols)>=8){
            $action_lower=strtolower($cols[1]);
            if($cols[1]!=='action'&&strpos($cols[0],'31-12-69')===false&&$action_lower!=='rh_login'&&$action_lower!=='rh_logout'){
                $logs[]=['timestamp'=>$cols[0],'action'=>$cols[1],'cpf'=>$cols[2],'matricula'=>$cols[3],'nome'=>$cols[4],'remote_addr'=>$cols[5],'user_agent'=>$cols[6],'extra'=>$cols[7]];
            }
        }
    }
    $logs=array_reverse($logs);
}
if($q!==''){
    $ql=strtolower($q);
    $tmp=[];
    foreach($logs as $log){
        if(strpos(strtolower($log['nome'].' '.$log['cpf'].' '.$log['matricula']),$ql)!==false)$tmp[]=$log;
    }
    $logs=$tmp;
}
if($action_filter!==''){
    $tmp=[];
    foreach($logs as $log){
        if($log['action']===$action_filter)$tmp[]=$log;
    }
    $logs=$tmp;
}
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#000080">
<title>Ver Logs - Área RH</title>
<style>
html{margin:0;padding:0}
body{color:#fff;font-family:Arial,Helvetica,sans-serif;padding:40px;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;margin:0;background-image:url('./imgs/fundo.png');background-size:cover;background-position:center center;background-repeat:no-repeat;background-attachment:fixed}
.logo-wrapper{text-align:center;padding:18px;width:100%;}
.logo-wrapper img{max-height:86px;display:inline-block;margin:0 auto;}
.wrap{background:#fff;color:#222;border-radius:18px;padding:42px 46px;box-shadow:0 12px 40px rgba(0,0,0,.35);max-width:1200px;width:100%;box-sizing:border-box;margin:18px auto}
h1{text-align:center;color:#000080;margin-bottom:18px}
.nav{display:flex;gap:12px;justify-content:center;margin-bottom:22px;flex-wrap:wrap}
.btn{background:#000080;color:#FFD700;padding:12px 20px;border-radius:8px;text-decoration:none;font-weight:600;transition:.18s;display:inline-block;white-space:nowrap;text-align:center}
.btn:hover{opacity:.9}
.danger{background:#c62828!important;color:#fff!important}
table{width:100%;border-collapse:collapse;margin-top:18px}
th,td{padding:12px 10px;text-align:left;border-bottom:1px solid #e0e0e0;word-wrap:break-word}
th{background:#000080;color:#FFD700;position:sticky;top:0}
tr:nth-child(even){background:#f7f9fc}
.table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
.search-form{background:#f7f9fc;padding:20px;border-radius:10px;margin-bottom:20px;display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.search-form input{padding:12px;border:1px solid #d0d5db;border-radius:6px;flex:1;min-width:250px;font-size:15px}
.search-form select{padding:12px;border:1px solid #d0d5db;border-radius:6px;font-size:15px;min-width:150px}
.search-form button{background:#000080;color:#FFD700;padding:12px 20px;border:none;border-radius:6px;cursor:pointer;font-weight:600;white-space:nowrap}
.search-form a{padding:12px 20px;background:#666;color:#fff;text-decoration:none;border-radius:6px;white-space:nowrap}
@media (max-width:768px){
body{padding:10px;margin:0;background-image:url('./imgs/fundo.png');background-size:cover;background-position:center center;background-repeat:no-repeat;background-attachment:fixed}
.wrap{padding:15px 12px;border-radius:12px;background:#fff;color:#222}
h1{font-size:1.3rem;margin-bottom:10px}
.nav{gap:8px}
.btn{width:100%;padding:12px 14px;font-size:14px;box-sizing:border-box}
.search-form{flex-direction:column;padding:12px;gap:8px}
.search-form input{width:100%;min-width:100%;box-sizing:border-box}
.search-form select{width:100%;box-sizing:border-box}
.search-form button,.search-form a{width:100%;text-align:center;box-sizing:border-box}
.table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;border-radius:8px}
table{font-size:12px;min-width:600px}
th,td{padding:10px 8px;font-size:12px;white-space:nowrap}
}
</style>
</head>
<body>
<div class="logo-wrapper"><img src="/imgs/logo.png" alt="Logo"></div>
<div class="wrap">
<h1>Ver Logs - Brindes</h1>
<p style="text-align:center;margin-bottom:20px">Usuário: <strong><?=htmlspecialchars($_SESSION['rh_user'])?></strong></p>
<div class="nav">
<a class="btn" href="rh.php">Painel RH</a>
<a class="btn" href="rh_funcionarios.php">Verificar Funcionários</a>
</div>
<form method="GET" class="search-form">
<input type="text" name="q" placeholder="Buscar nome, CPF ou matrícula" value="<?=htmlspecialchars($q)?>">
<select name="action">
<option value="">Todas as ações</option>
<option value="QR_GENERATED" <?=$action_filter==='QR_GENERATED'?'selected':''?>>QR Gerado</option>
<option value="DAR_BAIXA" <?=$action_filter==='DAR_BAIXA'?'selected':''?>>Dar Baixa</option>
</select>
<button type="submit">Buscar</button>
<?php if($q||$action_filter):?><a href="rh_logs.php">Limpar</a><?php endif;?>
</form>
<h2 style="text-align:center;color:#000080">Total de Registros: <?=count($logs)?></h2>
<div class="table-wrap">
<table>
<tr><th>Data/Hora</th><th>Ação</th><th>Nome</th><th>CPF</th><th>Matrícula</th><th>Usuário RH</th><th>Detalhes</th></tr>
<?php if(empty($logs)): ?>
<tr><td colspan="7" style="text-align:center;padding:30px;color:#666">Nenhum log encontrado</td></tr>
<?php else: ?>
<?php foreach($logs as $log): ?>
<tr>
<td><?php $dt=$log['timestamp'];if($dt){$p=explode(' ',$dt);echo htmlspecialchars(date('d-m-y',strtotime($p[0])).(isset($p[1])?' '.$p[1]:''));}?></td>
<td><?=htmlspecialchars($log['action'])?></td>
<td><?=htmlspecialchars($log['nome'])?></td>
<td><?=htmlspecialchars($log['cpf'])?></td>
<td><?=htmlspecialchars($log['matricula'])?></td>
<td><?php if(isset($log['extra'])&&preg_match('/RH:\s*(\S+)/',$log['extra'],$m)){echo htmlspecialchars($m[1]);}else{echo '-';}?></td>
<td><?=isset($log['extra'])?htmlspecialchars($log['extra']):'-'?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>
</div>
</div>
</body>
</html>
