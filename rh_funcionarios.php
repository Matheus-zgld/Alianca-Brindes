<?php
session_start();
if(empty($_SESSION['rh_user'])){header('Location: rh_login.php');exit;}
$q='';$status='';$funcionarios=[];
if(isset($_GET['q']))$q=trim($_GET['q']);
if(isset($_GET['status']))$status=$_GET['status'];
try{
    $pdo=new PDO('sqlite:'.__DIR__.'/brindes.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $rows=$pdo->query('SELECT nome_completo,cpf,matricula,brinde_status,data_resgate FROM funcionarios ORDER BY nome_completo')->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $r){
        $funcionarios[]=['nome_completo'=>$r['nome_completo'],'cpf'=>$r['cpf'],'matricula'=>$r['matricula'],'brinde_status'=>intval($r['brinde_status']),'data_resgate'=>$r['data_resgate']];
    }
    if($q){
        $ql=strtolower($q);$tmp=[];
        foreach($funcionarios as $f){
            if(strpos(strtolower($f['nome_completo'].' '.$f['cpf'].' '.$f['matricula']),$ql)!==false)$tmp[]=$f;
        }
        $funcionarios=$tmp;
    }
    if($status==='0'||$status==='1'){
        $tmp=[];
        foreach($funcionarios as $f){
            if(strval($f['brinde_status'])===$status)$tmp[]=$f;
        }
        $funcionarios=$tmp;
    }
    $total=count($funcionarios);$resgatados=0;
    foreach($funcionarios as $f){if($f['brinde_status']===1)$resgatados++;}
    $pendentes=$total-$resgatados;
}catch(Throwable $e){
    die('Erro: '.htmlspecialchars($e->getMessage()));
}
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#000080">
<title>Verificar Funcionários - Área RH</title>
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
.search-form input{padding:12px;border:1px solid #d0d5db;border-radius:6px;flex:1;min-width:200px;font-size:15px}
.search-form select{padding:12px;border:1px solid #d0d5db;border-radius:6px;font-size:15px}
.search-form button{background:#000080;color:#FFD700;padding:12px 20px;border:none;border-radius:6px;cursor:pointer;font-weight:600;white-space:nowrap}
.search-form a{padding:12px 20px;background:#666;color:#fff;text-decoration:none;border-radius:6px;white-space:nowrap}
.stats{display:flex;gap:15px;justify-content:center;margin:20px 0;flex-wrap:wrap}
.stat-box{background:#f7f9fc;padding:15px 25px;border-radius:10px;text-align:center;min-width:120px;flex:1}
.stat-box h3{color:#000080;margin:0 0 5px 0;font-size:2em}
.stat-box p{color:#666;margin:0;font-size:0.9em}
.badge-resgatado{background:#d4edda;color:#155724;padding:6px 12px;border-radius:20px;font-weight:600;font-size:0.85em;display:inline-block}
.badge-pendente{background:#f8d7da;color:#721c24;padding:6px 12px;border-radius:20px;font-weight:600;font-size:0.85em;display:inline-block}
@media (max-width:768px){
body{padding:10px;margin:0;background-image:url('./imgs/fundo.png');background-size:cover;background-position:center center;background-repeat:no-repeat;background-attachment:fixed}
.wrap{padding:15px 12px;border-radius:12px;background:#fff;color:#222}
h1{font-size:1.3rem;margin-bottom:10px}
.nav{gap:8px}
.btn{width:100%;padding:12px 14px;font-size:14px;box-sizing:border-box}
.search-form{flex-direction:column;padding:12px;gap:8px}
.search-form input,.search-form select{width:100%;min-width:100%;box-sizing:border-box}
.search-form button,.search-form a{width:100%;text-align:center;box-sizing:border-box}
.stats{flex-direction:row;gap:8px;margin:15px 0}
.stat-box{padding:10px 15px;min-width:0;flex:1}
.stat-box h3{font-size:1.3em}
.stat-box p{font-size:0.75em}
.table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;border-radius:8px}
table{font-size:12px;min-width:650px}
th,td{padding:10px 8px;font-size:12px;white-space:nowrap}
}
</style>
</head>
<body>
<div class="logo-wrapper"><img src="/imgs/logo.png" alt="Logo"></div>
<div class="wrap">
<h1>Verificar Funcionários - Brindes</h1>
<p style="text-align:center;margin-bottom:20px">Usuário: <strong><?=htmlspecialchars($_SESSION['rh_user'])?></strong></p>
<div class="nav">
<a class="btn" href="rh.php">Painel RH</a>
<a class="btn" href="rh_logs.php">Ver Logs</a>
</div>
<div class="stats">
<div class="stat-box">
<h3><?=$total?></h3>
<p>Total</p>
</div>
<div class="stat-box">
<h3 style="color:#28a745"><?=$resgatados?></h3>
<p>Resgatados</p>
</div>
<div class="stat-box">
<h3 style="color:#dc3545"><?=$pendentes?></h3>
<p>Pendentes</p>
</div>
</div>
<form method="GET" class="search-form">
<input type="text" name="q" placeholder="Buscar nome, CPF ou matrícula" value="<?=htmlspecialchars($q)?>">
<select name="status">
<option value="">Todos</option>
<option value="1" <?=$status==='1'?'selected':''?>>Resgatados</option>
<option value="0" <?=$status==='0'?'selected':''?>>Pendentes</option>
</select>
<button type="submit">Filtrar</button>
<?php if($q||$status!==''):?><a href="rh_funcionarios.php">Limpar</a><?php endif;?>
</form>
<div class="table-wrap">
<table>
<tr><th>Nome</th><th>CPF</th><th>Matrícula</th><th>Status</th><th>Data Resgate</th></tr>
<?php if(empty($funcionarios)): ?>
<tr><td colspan="5" style="text-align:center;padding:30px;color:#666">Nenhum funcionário encontrado</td></tr>
<?php else: ?>
<?php foreach($funcionarios as $f): ?>
<tr>
<td><?=htmlspecialchars($f['nome_completo'])?></td>
<td><?=htmlspecialchars($f['cpf'])?></td>
<td><?=htmlspecialchars($f['matricula'])?></td>
<td><?=$f['brinde_status']===1?'<span class="badge-resgatado">Resgatado</span>':'<span class="badge-pendente">Pendente</span>'?></td>
<td><?php $dt=$f['data_resgate'];if($dt){$p=explode(' ',$dt);echo htmlspecialchars(date('d-m-y',strtotime($p[0])).(isset($p[1])?' '.$p[1]:''));}else{echo '-';}?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>
</div>
</div>
</body>
</html>
