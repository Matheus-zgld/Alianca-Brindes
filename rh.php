<?php
session_start();
if(empty($_SESSION['rh_user'])) { header('Location: rh_login.php'); exit; }

$error='';$info='';$qr_data='';$funcionario=null;$resgatado=false;$resgatados=[];

try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/brindes.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if(isset($_POST['acao']) && $_POST['acao']==='baixar' && isset($_POST['cpf'],$_POST['matricula'])) {
        $cpf=preg_replace('/\D/','',$_POST['cpf']);
        $matricula=preg_replace('/\D/','',$_POST['matricula']);
        $st=$pdo->prepare('SELECT * FROM funcionarios WHERE cpf=? AND matricula=?');
        $st->execute([$cpf,$matricula]);
        $f=$st->fetch(PDO::FETCH_ASSOC);
        if(!$f){
            $error='Funcionário não encontrado';
        } elseif(intval($f['brinde_status'])===1){
            $error='Brinde já entregue';
        } else {
            $pdo->prepare('UPDATE funcionarios SET brinde_status=1, data_resgate=datetime("now","localtime") WHERE cpf=? AND matricula=?')->execute([$cpf,$matricula]);
            $log_file=__DIR__.'/data_log.csv';
            $ts=date('Y-m-d H:i:s');
            $remote=isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';
            $ua=isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
            $rh_user=$_SESSION['rh_user'];
            $fh=fopen($log_file,'a');
            fputcsv($fh,[$ts,'DAR_BAIXA',$cpf,$matricula,$f['nome_completo'],$remote,$ua,'Baixa confirmada por RH: '.$rh_user]);
            fclose($fh);
            $info='Brinde entregue com sucesso';
        }
    }
    
    if(isset($_POST['qr_data'])) {
        $qr_data=trim($_POST['qr_data']);
        if($qr_data!=='') {
            $cpf='';$matricula='';
            if(strpos($qr_data,':')!==false){
                list($cpf,$matricula)=explode(':',$qr_data,2);
                $cpf=preg_replace('/\D/','',$cpf);
                $matricula=preg_replace('/\D/','',$matricula);
            }
            if(strlen($cpf)===11 && strlen($matricula)>=1){
                $st=$pdo->prepare('SELECT * FROM funcionarios WHERE cpf=? AND matricula=?');
                $st->execute([$cpf,$matricula]);
                $funcionario=$st->fetch(PDO::FETCH_ASSOC);
                if($funcionario){
                    $resgatado=intval($funcionario['brinde_status'])===1;
                } else {
                    $error='Funcionário não encontrado';
                }
            } else {
                $error='Formato inválido. Use: CPF:Matricula';
            }
        }
    }
    
    $st = $pdo->query('SELECT nome_completo, matricula, cpf, data_resgate FROM funcionarios WHERE brinde_status=1 ORDER BY data_resgate DESC LIMIT 20');
    $resgatados = $st->fetchAll(PDO::FETCH_ASSOC);
} catch(Throwable $e) {
    die('Erro: ' . htmlspecialchars($e->getMessage()));
}
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Área RH</title>
<style>
body{background:linear-gradient(135deg,#000080 0%,#0000ad 100%);color:#fff;font-family:Arial,Helvetica,sans-serif;padding:40px;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;margin:0}
.logo-wrapper{text-align:center;padding:18px;width:100%;}
.logo-wrapper img{max-height:86px;display:inline-block;margin:0 auto;}
.wrap{background:#fff;color:#222;border-radius:18px;padding:42px 46px;box-shadow:0 12px 40px rgba(0,0,0,.35);max-width:900px;width:100%;box-sizing:border-box;margin:18px auto}
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
#reader{min-height:250px}
@media (max-width:768px){
body{padding:15px}
.wrap{padding:20px 15px;border-radius:12px}
h1{font-size:1.4rem;margin-bottom:12px}
.nav{gap:8px}
.btn{width:100%;padding:14px 16px;font-size:14px}
table{font-size:12px;display:table}
th,td{padding:10px 8px;font-size:12px}
th{white-space:nowrap}
td{word-break:break-word}
#reader{max-width:100%;min-height:200px}
}
</style>
</head>
<body>
<div class="logo-wrapper"><img src="/imgs/logo.png" alt="Logo"></div>
<div class="wrap">
<h1>Área RH - Brindes</h1>
<p style="text-align:center;margin-bottom:20px">Usuário: <strong><?=htmlspecialchars($_SESSION['rh_user'])?></strong></p>
<?php if($error):?><div style="background:#ffecec;color:#b00020;padding:12px;border-radius:10px;margin-bottom:16px;text-align:center;font-weight:600"><?=htmlspecialchars($error)?></div><?php endif;?>
<?php if($info):?><div style="background:#e7f6ff;color:#01579b;padding:12px;border-radius:10px;margin-bottom:16px;text-align:center;font-weight:600"><?=htmlspecialchars($info)?></div><?php endif;?>
<div class="nav">
<a class="btn" href="index.php">Área do Funcionário</a>
<a class="btn" href="rh_funcionarios.php">Verificar Funcionários</a>
<a class="btn" href="rh_logs.php">Ver Logs</a>
<a class="btn danger" href="rh_logout.php">Sair</a>
</div>
<div style="background:#f7f9fc;border:2px solid #ebeff5;border-radius:14px;padding:22px;margin:20px 0">
<h3 style="color:#000080;margin-bottom:14px;text-align:center">Scanner QR Code</h3>
<div id="reader" style="width:100%;max-width:400px;margin:0 auto 20px auto"></div>
<p style="text-align:center;font-size:13px;color:#666;margin-bottom:20px">Tire uma foto do QR Code ou faça upload de uma imagem</p>
<h3 style="color:#000080;margin-bottom:14px">Ou digite manualmente</h3>
<form method="POST" autocomplete="off" id="form-verificar">
<input type="text" name="qr_data" placeholder="CPF:Matricula (ex: 00000000000:123456)" value="<?=htmlspecialchars($qr_data)?>" style="width:100%;max-width:100%;padding:12px;font-size:15px;border:2px solid #d0d5db;border-radius:10px;margin-bottom:10px;box-sizing:border-box" required>
<button type="submit" style="width:100%;background:#000080;color:#FFD700;padding:12px;border:none;border-radius:8px;font-weight:700;font-size:15px;cursor:pointer">Verificar</button>
</form>
<?php if($funcionario):?>
<div style="margin-top:18px;padding:18px;border:2px solid #000080;border-radius:14px;background:#fff7d1">
<p><strong>Nome:</strong> <?=htmlspecialchars($funcionario['nome_completo'])?></p>
<p><strong>CPF:</strong> <?=htmlspecialchars($funcionario['cpf'])?></p>
<p><strong>Matrícula:</strong> <?=htmlspecialchars($funcionario['matricula'])?></p>
<p><strong>Status:</strong> <?=$resgatado?'<span style="color:#1b5e20;font-weight:700">Brinde já entregue</span>':'<span style="color:#b00020;font-weight:700">Pendente</span>'?></p>
<?php if(!$resgatado):?>
<form method="POST" style="margin-top:14px">
<input type="hidden" name="acao" value="baixar">
<input type="hidden" name="cpf" value="<?=htmlspecialchars($funcionario['cpf'])?>">
<input type="hidden" name="matricula" value="<?=htmlspecialchars($funcionario['matricula'])?>">
<button type="submit" style="width:100%;background:#000080;color:#FFD700;padding:12px;border:none;border-radius:8px;font-weight:700;cursor:pointer">Dar Baixa no Brinde</button>
</form>
<?php endif;?>
</div>
<?php endif;?>
</div>
<h2 style="margin-top:34px;text-align:center">Últimos Brindes Entregues (<?=count($resgatados)?>)</h2>
<div class="table-wrap">
<table>
<tr><th>Nome</th><th>Matrícula</th><th>CPF</th><th>Data</th></tr>
<?php foreach($resgatados as $r): ?>
<tr>
<td><?=htmlspecialchars($r['nome_completo'])?></td>
<td><?=htmlspecialchars($r['matricula'])?></td>
<td><?=htmlspecialchars($r['cpf'])?></td>
<td><?php $dt=$r['data_resgate'];if($dt){$p=explode(' ',$dt);echo htmlspecialchars(date('d-m-y',strtotime($p[0])).(isset($p[1])?' '.$p[1]:''));}?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
</div>
</body>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded',function(){
  var reader=document.getElementById('reader');
  if(!reader)return;
  function onSuccess(txt){
    var inp=document.querySelector('input[name="qr_data"]');
    if(inp){inp.value=txt;document.getElementById('form-verificar').submit();}
  }
  function onFail(){}
  try{
        var isSecure=window.location.protocol==='https:'||window.location.hostname==='localhost';
        var isMobile=/iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        if(!isSecure&&isMobile){
            reader.innerHTML='<input type="file" accept="image/*" capture="environment" style="width:100%;padding:15px;border:2px dashed #000080;border-radius:10px;background:#fff;cursor:pointer" id="fileInput"><p style="text-align:center;margin-top:10px;color:#666;font-size:13px">Tire uma foto do QR Code</p>';
            document.getElementById('fileInput').addEventListener('change',function(e){
                var file=e.target.files[0];
                if(file){
                    reader.innerHTML='<p style="text-align:center;color:#000080;margin:20px 0">Processando imagem...</p>';
                    var html5QrCode=new Html5Qrcode('reader');
                    html5QrCode.scanFile(file,true).then(function(txt){
                        onSuccess(txt);
                    }).catch(function(err){
                        reader.innerHTML='<div style="background:#ffecec;color:#b00020;padding:12px;border-radius:8px;margin:10px 0;text-align:center">Erro ao ler QR Code. Tente novamente com melhor iluminação.</div><input type="file" accept="image/*" capture="environment" style="width:100%;padding:15px;border:2px dashed #000080;border-radius:10px;background:#fff;cursor:pointer;font-size:14px;margin-top:10px" id="fileInput2">';
                        document.getElementById('fileInput2').addEventListener('change',arguments.callee);
                    });
                }
            });
        }else{
            var config={fps:10,qrbox:isMobile?{width:200,height:200}:{width:250,height:250},rememberLastUsedCamera:true,showTorchButtonIfSupported:true,aspectRatio:1.0};
            var scanner=new Html5QrcodeScanner('reader',config,false);
            scanner.render(onSuccess,onFail);
        }
  }catch(e){
    reader.innerHTML='<p style="color:#b00020;text-align:center">Erro: '+e.message+'</p>';
  }
});
</script>
</html>


