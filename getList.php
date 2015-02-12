<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

header('Content-Type:text/html;charset=utf-8');

include_once 'bdListModel.php';

$bdListModel = new bdListModel();

$maxp = 100;
$filename = "q.txt";
$file = file_get_contents($filename);
$qs = preg_split('/\\n/', $file);
foreach ($qs as &$v){
    $v = trim($v);
}
$regions = array(
    //'北京',
    //'上海',
    //'广州',
    '深圳'
    );

foreach($regions as $region){
    foreach ($qs as $q){
        $bdListModel->getList($q, $region, $maxp);
    }
}

?>
