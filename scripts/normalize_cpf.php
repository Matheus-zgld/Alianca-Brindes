<?php
$src = __DIR__ . '/../brindes.db';
$bak = __DIR__ . '/../brindes.db.bak2';
if(!file_exists($bak)) {
    if(!copy($src, $bak)) {
        echo "Failed to create backup at $bak\n"; exit(1);
    }
    echo "Backup created: $bak\n";
} else {
    echo "Backup already exists: $bak\n";
}
try{
    $db = new PDO('sqlite:' . $src);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $rows = $db->query('SELECT rowid, cpf FROM funcionarios')->fetchAll(PDO::FETCH_ASSOC);
    $cnt = 0;
    foreach($rows as $r) {
        $clean = preg_replace('/\D/', '', $r['cpf']);
        if(strlen($clean) < 11) {
            $clean = str_pad($clean, 11, '0', STR_PAD_LEFT);
        } else {
            $clean = substr($clean, -11);
        }
        if($clean !== $r['cpf']) {
            $upd = $db->prepare('UPDATE funcionarios SET cpf = ? WHERE rowid = ?');
            $upd->execute([$clean, $r['rowid']]);
            $cnt++;
        }
    }
    echo "Normalized $cnt CPF entries.\n";
} catch(Exception $e) { echo 'ERR: '.$e->getMessage()."\n"; }
