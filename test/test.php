<?php
include_once ('../vendor/autoload.php');

use marshung\finance\Grab;

$g = new Grab();

echo "<pre>";

var_export($g->grab());