<?php
include_once('../vendor/autoload.php');

use marsapp\grab\finance\Grab;

$g = new Grab();

echo "<pre>";

// $a = file_get_contents('https://www.twse.com.tw/exchangeReport/MI_INDEX?response=json&date=20181109&type=MS&_=1575086249135');
// var_export($a);exit;

$t1 = microtime(true);

$data = $g->grab('2018-11-09', 'MS');


$t2 = microtime(true);


echo $t2 - $t1;
echo "\n\n";


var_export($data);
