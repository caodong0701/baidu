<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'DBOperator.class.php';
include_once 'bdapiModel.php';

$db = new DBOperator("127.0.0.1", "root", "caodong0701", "bdlist");
$bdapiModel = new bdapiModel();

$sqlbizs = "select * from uids where detail_state=0 limit 1;";

$query = $db->runQuery($sqlbizs);

while($row = $query->fetch_array()){
    $bid = $row['bid'];
    $uid = $row['uid'];
    $html = $bdapiModel->getDetailHtml($uid);
    $normal = $bdapiModel->extractDetailHtml($html);
    if(is_array($normal) && !empty($normal)){
        $r1 = $bdapiModel->toDbBizs($normal);
        $r2 = $bdapiModel->toDbBizDetail($normal);
        $r3 = $bdapiModel->toDbBizReview($normal);
        $r4 = $bdapiModel->toDbBizImg($normal);
        if($r1 && $r2 && $r3 && $r4){
            $sql = "update uids set updatetime=now(),detail_state=1 where uid='{$uid}';";
            $db->runQuery($sql);
        }
    }
}



?>
