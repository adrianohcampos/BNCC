<?php

include('vendor/autoload.php');

use AdrianoHCampos\BNCC\BNCC;

$bncc = new BNCC;

// $ensinoInfantil = $bncc->buscaDados('infantil', [1, 2, 3], [1, 2, 3, 4, 5]);

// $ensinoFundamental = $bncc->buscaDados('fundamental', [1, 2, 3, 4, 5, 6, 7, 8, 9], [1, 2, 3, 4, 5, 6, 7, 8, 9]);

// $ensinoMedio = $bncc->buscaDados('medio', [1, 2, 3], [36, 37, 38, 39, 40]);

echo '<pre>';
print_r($ensinoFundamental);
echo '</pre>';
exit;
