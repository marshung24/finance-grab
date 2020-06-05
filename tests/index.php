<?php
include_once('../vendor/autoload.php');

use marsapp\grab\finance\Grab;

$g = new Grab();

echo "<pre>";

// $a = file_get_contents('https://www.twse.com.tw/exchangeReport/MI_INDEX?response=json&date=20181109&type=MS&_=1575086249135');
// var_export($a);exit;

$t1 = microtime(true);

// 抓取 TWSE 臺灣證券交易所資料
// $data = $g->grab('2018-11-09', 'ALLBUT0999');

// 抓取 道瓊工業平均指數 資料
// $data = $g->grabDjia('2020-03-12');


// 抓取 台灣證券交易所 三大法人買賣超日報
$data = $g->grabTwseCorp3('2020-06-05');


$t2 = microtime(true);


echo $t2 - $t1;
echo "\n\n";


var_export($data);
