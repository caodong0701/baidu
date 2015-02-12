<?php

/**
 * Description of bdapiModel
 *
 * @author caodong
 */
/**
  meishi
  scope
  hotel
  shopping
 */
header('Content-Type:text/html;charset=utf-8');
include_once 'DBOperator.class.php';

class bdapiModel {

    public $db;

    public function __construct() {
        $this->db = new DBOperator("127.0.0.1", "root", "caodong0701", "bdlist");
    }

    public function getDetailHtml($uid) {
        if (!$uid) {
            return false;
        }
        $url = "http://map.baidu.com/detail?qt=ninf&uid={$uid}";
        $html = file_get_contents($url);
        return $html;
    }

    public function extractDetailHtml($html) {
        if (!$html) {
            return fasle;
        }
        $array = json_decode($html, true);
        //print_r($array);

        if (!$array['content']) {
            return false;
        }
        $normal = array();
        $content = $array['content'];
        $normal['uid'] = $content['uid'];
        $normal['city_id'] = $content['city_id'];
        $normal['area'] = $content['area'];
        $normal['name'] = $content['name'];
        $normal['addr'] = $content['addr'];
        $normal['phone'] = $content['phone'];
        if (is_array($content['ext']) && !empty($content['ext'])) {
            $ext = $content['ext'];
            $src_name = $ext['src_name'];
            $detail_info = $ext['detail_info'];
            $normal = array_merge($normal, $this->extractDetailInfo($detail_info));
            $normal['other_uids'] = $this->extractNewUids($detail_info);
            $rich_info = $ext['rich_info'];
            $normal = array_merge($normal, $this->extractRichInfo($rich_info, $src_name));

            $normal['src_name'] = $src_name;
            $review = $ext['review'];
            $normal = array_merge($normal, $this->extractReview($review, $src_name));
            $image = $ext['image'];
            $normal = array_merge($normal, $this->extractImg($image, $src_name));
        }
        return $normal;
    }

    public function extractDetailInfo($detail_info) {
        if (!is_array($detail_info) || empty($detail_info)) {
            return array();
        }
        $normal = array();
        $normal['overall_rating'] = $detail_info['overall_rating'];
        $normal['taste_rating'] = $detail_info['taste_rating'];
        $normal['service_rating'] = $detail_info['service_rating'];
        $normal['environment_rating'] = $detail_info['environment_rating'];
        $normal['image'] = $detail_info['image'];
        $normal['price'] = $detail_info['price'];
        $normal['tag'] = $detail_info['tag'];
        $normal['status'] = $detail_info['status'];
        if (is_array($detail_info['di_review_keyword'])) {
            $review_keyword = array();
            foreach ($detail_info['di_review_keyword'] as $value) {
                $review_keyword[] = $value['keyword'];
            }
            $normal['review_keyword'] = $review_keyword;
        }

        return $normal;
    }

    public function extractRichInfo($rich_info, $src_name = '') {
        if (!is_array($rich_info) || empty($rich_info)) {
            return array();
        }
        $normal = array();
        $normal['description'] = $rich_info['description'];
        $normal['atmosphere'] = $rich_info['atmosphere'];
        $normal['shop_hours'] = $rich_info['shop_hours'];
        $normal['alias'] = $rich_info['alias'];
        $normal['business_state'] = $rich_info['business_state'];
        $normal['featured_service'] = $rich_info['featured_service'];
        $normal['recommendation'] = $rich_info['recommendation'];
        if ($src_name == 'scope') {
            $normal['scope_type'] = $rich_info['scope_type'];
        }
        if ($src_name == 'hotel') {
            $hotel = array();
            $hotel['category'] = $rich_info['category'];
            $hotel['brand'] = $rich_info['brand'];
            $hotel['level'] = $rich_info['level'];
            $hotel['environment_exterior'] = $rich_info['environment_exterior'];
            $hotel['inner_facility'] = $rich_info['inner_facility'];
            $hotel['hotel_facility'] = $rich_info['hotel_facility'];
            $hotel['hotel_service'] = $rich_info['hotel_service'];
            $hotel['payment_type'] = $rich_info['payment_type'];
            $normal['hotel'] = $hotel;
        }
        if ($src_name == 'hospital') {
            $hospital = array();
            $hospital['hospital_degree'] = $rich_info['hospital_degree'];
            $hospital['hospital_type'] = $rich_info['hospital_type'];
            $hospital['hospital_character'] = $rich_info['hospital_character'];
            $hospital['hotline'] = $rich_info['hotline'];
            $hospital['referral_appointment'] = $rich_info['referral_appointment'];
            $hospital['gynecology_pediatrics_departments'] = $rich_info['gynecology_pediatrics_departments'];
            $hospital['internal_medicine_departments'] = $rich_info['internal_medicine_departments'];
            $hospital['other_departments'] = $rich_info['other_departments'];
            $hospital['recommended_experts'] = $rich_info['recommended_experts'];
            $hospital['surgical_departments'] = $rich_info['surgical_departments'];
            $hospital['traditional_chinese_medicine'] = $rich_info['traditional_chinese_medicine'];
            $normal['hospital'] = $hospital;
        }
        return $normal;
    }

    public function extractReview($review, $src_name = '') {
        if (!is_array($review) || empty($review)) {
            return array();
        }
        $normal = array();
        $reviews = array();
        //if ($src_name == 'scope' || $src_name == "hotel" || $src_name=="hospital" || $src_name=="education") {
        foreach ($review as $v1) {
            if (!is_array($v1['info'])) {
                continue;
            }
            foreach ($v1['info'] as $v2) {
                if (!$v2['content']) {
                    continue;
                }
                $reviews[] = array(
                    'content' => $v2['content'],
                    'date' => $v2['date'],
                    'user_name' => $v2['user_name'],
                );
            }
        }
        foreach ($review as $v1) {
            if (!$v1['content']) {
                continue;
            }
            $reviews[] = array(
                'content' => $v1['content'],
                'date' => $v1['date'],
                'user_name' => $v1['user_name'],
            );
        }
        $normal['review'] = $reviews;
        return $normal;
    }

    public function extractImg($img, $src_name = '') {
        if (!is_array($img) || empty($img)) {
            return array();
        }
        $normal = array();
        $imgs = array();
        foreach ($img as $v1) {
            foreach ($v1 as $v2) {
                if (!$v2['imgUrl']) {
                    continue;
                }
                $imgs[] = array(
                    'imgUrl' => $v2['imgUrl'],
                    'cn_name' => $v2['cn_name'],
                    'username' => $v2['username'],
                    'commodity_name' => $v2['commodity_name'],
                    'photo_uploadtime' => $v2['photo_uploadtime'],
                );
            }
        }
        $normal['img'] = $imgs;
        return $normal;
    }

    public function extractNewUids($detail_info) {
        if (!is_array($detail_info) || empty($detail_info)) {
            return array();
        }
        $uids = array();
        if (is_array($detail_info['nearby'])) {
            foreach ($detail_info['nearby'] as $v1) {
                $uids[] = $v1['uid'];
            }
        }
        if (is_array($detail_info['toplist'])) {
            if (is_array($detail_info['toplist']['top'])) {
                $uids[] = $detail_info['toplist']['top']['uid'];
            }
            if (is_array($detail_info['toplist']['list'])) {
                foreach ($detail_info['toplist']['list'] as $v2) {
                    $uids[] = $v2['uid'];
                }
            }
        }
        return $uids;
    }

    public function addNewUid($uidArray) {
        if (!is_array($uidArray) || empty($uidArray)) {
            return false;
        }
        foreach ($uidArray as $value) {
            $uid = $value['uid'];
            $this->addUid($uid);
        }
    }

    public function addUid($uid) {
        if (!$uid) {
            return false;
        }
        $sql = "insert into uids(uid) values('{$uid}');";
        return $this->db->runQuery($sql);
    }

    public function toDbBizs($normal=array()) {
        if(empty($normal)){
            return false;
        }
        
        return true;
    }

    public function toDbBizDetail($normal=array()) {
        if(empty($normal)){
            return false;
        }
        
        return true;
    }

    public function toDbBizReview($normal=array(),$bid) {
        if(empty($normal['review'])){
            return true;
        }
        $reviews = $normal['review'];
        $sql = array();
        foreach($reviews as $value){
            $content = $value['content'];
            $date = $value['date'];
            $user_name = $value['user_name'];
            if(!$content){
                continue;
            }
            $sign = md5($content);
            $content = $this->db->escape($content);
            $date = $this->db->escape($date);
            $user_name = $this->db->escape($user_name);
            $sql[] = "replace into biz_review(bid,content,date,user_name,sign) values('{$bid}','{$content}','{$date}','{$user_name}','{$sign}');";
        }
        if(!empty($sql)){
            return $this->db->runMultiQuery($sql);
        }
        return true;
    }

    public function toDbBizImg($normal=array(),$bid) {
        if(empty($normal['imgs'])){
            return true;
        }
        if(empty($normal['imgs'])){
            return true;
        }
        $imgs = $normal['imgs'];
        $sql = array();
        foreach($imgs as $value){
            $imgUrl = $value['imgUrl'];
            $cn_name = $value['cn_name'];
            $username = $value['username'];
            $commodity_name = $value['commodity_name'];
            $photo_uploadtime = $value['photo_uploadtime'];
            if(!$imgUrl){
                continue;
            }
            $sign = md5($imgUrl);
            $imgUrl = $this->db->escape($imgUrl);
            $cn_name = $this->db->escape($cn_name);
            $username = $this->db->escape($username);
            $commodity_name = $this->db->escape($commodity_name);
            $photo_uploadtime = $this->db->escape($photo_uploadtime);
            $sql[] = "replace into biz_img(bid,imgUrl,cn_name,username,commodity_name,photo_uploadtime) values('{$bid}','{$imgUrl}','{$cn_name}','{$username}','{$commodity_name}','{$photo_uploadtime}','{$sign}');";
        }
        if(!empty($sql)){
            return $this->db->runMultiQuery($sql);
        }
        return true;
    }

}

$bdapiModel = new bdapiModel();
$uid = "75d0881a94e433543b04b1f5";
$html = $bdapiModel->getDetailHtml($uid);
$normal = $bdapiModel->extractDetailHtml($html);
print_r($normal);

?>
 
