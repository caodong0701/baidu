<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
header('Content-Type:text/html;charset=utf-8');
$q = "中国银行(北京通州梨园支行)";
$region = "北京";
$ak = "8d4dd1edd99b74ca8a521fcf6b2483ce";
$page_num = 0;
for($page_num = 0;$page_num<10;$page_num++){
    $results = getUids($q, $region, $ak, $page_num);
    if(!$results){
        break;
    }
}




function getUids($q,$region,$ak,$page_num){
    $results = getList($q, $region, $ak, $page_num);
    if(empty($results) || !is_array($results)){
        return false;
    }
    foreach($results as $value){
        $uid = $value['uid'];
        $name = $value['name'];
        $address = $value['address'];
        $telephone = $value['telephone'];
        $lat = $value['location']['lat'];
        $lng = $value['location']['lng'];
        $line = "{$uid} {$name} {$address} {$telephone} {$lat} {$lng}<br />";
        echo $line;
    }
    return $results;
}


function getList($q,$region,$ak,$page_num){
    $q = urlencode($q);
    $region = urlencode($region);
    $url = "http://api.map.baidu.com/place/v2/search?&q={$q}&region={$region}&output=json&ak={$ak}&page_size=20&page_num={$page_num}";
    $json = file_get_contents($url);
    if(!$json){
        return false;
    }
    $array = json_decode($json, true);
    $status = $array['status'];
    if($status != "0"){
        echo "status error!\n";
        exit();
    }
    return $array['results'];
}

?>
