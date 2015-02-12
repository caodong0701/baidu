<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$filename = "D:\cd\webroot\locc\baidu\mingcheng.txt";
$out = "D:\cd\webroot\locc\baidu\mingcheng_out.txt";
$file = fopen($filename, "r");
file_put_contents($out, "");
$it = 0;
while (!feof($file)) {
    $tmp = fgets($file);
    $line = split(" ", $tmp);
    $word = trim($line[0]);
    $res = hasAd($word);
    if($res){
        file_put_contents($out, $tmp, FILE_APPEND);
    }
    $it++;
    echo "{$it} {$word} ";
    var_dump($res);
}
fclose($file);

function hasAd($word) {
    $word = urlencode($word);
    $url = "http://www.baidu.com/s?wd={$word}&ie=utf-8";
    $html = file_get_contents($url);
    $pos1 = stripos($html, '<a href="http://e.baidu.com/?id=1" target="_blank" class="m">推广</a>');
    $pos2 = stripos($html, '<a target="_blank" href="http://e.baidu.com/?refer=666"><font color="#666666">百度推广链接</font></a>');
    unset($html);
    //var_dump($pos1);
    //var_dump($pos2);
    if ($pos1 || $pos2) {
        return true;
    }
    return false;
}

?>
