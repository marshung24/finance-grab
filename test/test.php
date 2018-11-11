<?php
include_once ('../vendor/autoload.php');

use marshung\finance\Grab;

$g = new Grab();

echo "<pre>";

$t1 = microtime(true);

$data = $g->grab('2018-11-09', 'MS');


$t2 = microtime(true);


echo $t2 - $t1;
echo "\n\n";


var_export($data);