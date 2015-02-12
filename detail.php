<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

header('Content-Type:text/html;charset=utf-8');

$url = "http://map.baidu.com/detail?qt=ninf&uid=6bce1076fcaa5e3d56bfff81";




//$url = "http://map.baidu.com/?newmap=1&qt=s&c=131&wd=%E9%A4%90%E9%A5%AE&pn=50nn=29&ie=utf-8";


$json = file_get_contents($url);

$array = json_decode($json,true);

print_r($array);


?>
