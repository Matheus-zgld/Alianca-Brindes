<?php
try{
    $db = new PDO('sqlite:' . __DIR__ . '/../brindes.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $s = $db->prepare('SELECT cpf, matricula, nome_completo, brinde_status FROM funcionarios WHERE cpf = ? AND matricula = ?');
    $s->execute(['16031109840', '013912']);
    $r = $s->fetch(PDO::FETCH_ASSOC);
    if($r) var_export($r);
    else echo "NOT FOUND";
} catch(Exception $e) { echo 'ERR: '.$e->getMessage(); }
