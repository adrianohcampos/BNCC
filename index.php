<?php

include('vendor/autoload.php');

use AdrianoHCampos\BNCC\BNCC;

$bncc = new BNCC;

$ensinoMedio = $bncc->buscaDados('fundamental', [1], [1]);

echo '<pre>';
print_r($ensinoMedio);
echo '</pre>';
exit;
