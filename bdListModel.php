<?php

/**
 * Description of bdListModel
 *
 * @author caodong
 */

include_once 'DBOperator.class.php';

class bdListModel {
    public $aks = array(
        "8d4dd1edd99b74ca8a521fcf6b2483ce",
        "F2b99c0340c69b936d3071ff363ddd9d",
        );
    public $db;
    public function __construct() {
        $this->db = new DBOperator("127.0.0.1","root","","bdlist");
    }
    
    public function getList($q,$region,$maxp=100){
        echo "{$region} {$q}\n";
        if(!$q || !$region){
            return false;
        }
        $ak_len = count($this->aks);
        for($p=0;$p<$maxp;$p++){
            $ak = $this->aks[$p%$ak_len];
            $bizs = $this->getBizList($q, $region, $ak,$p);
            if(!$bizs || empty($bizs)){
                break;
            }
            $this->bizListToDb($bizs);
        }
    }


    public function getBizList($q,$region,$ak,$page_num=0,$page_size=20){
        echo "{$region} {$q} {$page_num}\n";
        if(!$q || !$region || !$ak){
            return false;
        }
        $q = urlencode($q);
        $region = urldecode($region);
        $url = "http://api.map.baidu.com/place/v2/search?&q={$q}&region={$region}&output=json&ak={$ak}&scope=2&page_num={$page_num}&page_size={$page_size}";
        $json = file_get_contents($url);
        if(!$json){
            return false;
        }
        $array = json_decode($json, TRUE);
        if($array['status'] != 0){
            return false;
        }
        $results = $array['results'];
        if(empty($results)){
            return false;
        }
        foreach($results as $k=>$v){
            $results[$k]['city'] = $region;
        }
        return $results;
    }
    
    public function bizListToDb($bizs){
        if(empty($bizs) || !is_array($bizs)){
            return false;
        }
        foreach ($bizs as $value){
            $args = array();
            $args['uid'] = $uid = $value['uid'];
            if(!$uid){
                continue;
            }
            $select = "select uid from uids where uid='{$uid}' and state=1;";
            if($this->db->getOneFromSql($select)){
                echo "unique uid {$uid}\n";
                continue;
            }
            $args['city'] = $value['city'];
            $args['name'] = $value['name'];
            $args['address'] = $value['address'];
            $args['telephone'] = $value['telephone'];
            if($value['location']){
                $args['lat'] = (int)($value['location']['lat']*1000000);
                $args['lng'] = (int)($value['location']['lng']*1000000);
            }
            if($value['detail_info']){
                $args['type'] = $value['detail_info']['type'];
                $args['tag'] = $value['detail_info']['tag'];
            }
            foreach($args as $k=>$v){
                $args[$k] = "'".$this->db->escape($v)."'";
            }
            $keys = array_keys($args);
            $keys = implode(",", $keys);
            $vals = implode(",", $args);
            $sql = array();
            $sql[] = "replace into bizs({$keys},create_time) values({$vals},now());";
            $sql[] = "replace into uids(uid,state,update_time) values('{$value['uid']}','1',now());";
            $res = $this->db->runTransaction($sql);
            echo "insert uid: {$uid} ";
            var_dump($res);
        }
    }
    
}

?>
