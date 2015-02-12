<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 
 
header('Content-Type:text/html;charset=utf-8');

$url = "http://map.baidu.com/detail?qt=ninf&uid=850815e173d2168243e1926c";

$json = file_get_contents($url);

$array = json_decode($json, true);

print_r($array);





?>
