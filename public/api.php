<?php

$arr = [];

$len = $_GET['len'] ?? 4;

if ($len > 50) $len = 50;

for ($i=1; $i < $len+1; $i++) { 
    $arr[$i - 1]['name'] = '测试名称' . $i;
    $arr[$i - 1]['value'] = mt_rand(10000, 10000000) / 100;
    $arr[$i - 1]['time'] = date('m-d', strtotime('-'.$i.' day'));
}

$json = json_encode($arr, JSON_UNESCAPED_UNICODE);

exit($json);