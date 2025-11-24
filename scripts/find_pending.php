<?php
$pdo = new PDO('sqlite:' . __DIR__ . '/../brindes.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$r = $pdo->query("SELECT cpf, matricula, nome_completo FROM funcionarios WHERE brinde_status = 0 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if($r) echo $r['cpf'] . '|' . $r['matricula'] . '|' . $r['nome_completo'];
else echo '';
