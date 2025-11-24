<?php
try{
    $db = new PDO('sqlite:' . __DIR__ . '/../brindes.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $db->prepare('SELECT cpf, matricula, nome_completo, brinde_status FROM funcionarios WHERE matricula = ? LIMIT 5');
    $stmt->execute(['013912']);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(!$rows) { echo "NONE\n"; exit; }
    foreach($rows as $r) echo $r['cpf'].' | '.$r['matricula'].' | '.$r['nome_completo'].' | status='.$r['brinde_status']."\n";
} catch(Exception $e){ echo 'ERR: '.$e->getMessage(); }
