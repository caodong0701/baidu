<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of commonModel
 *
 * @author caodong
 */
class commonModel {
    //put your code here
    
    
    public static function distince($lat1,$lon1,$lat2,$lon2){
        
        $dis = 6378137*2*asin(sqrt(pow(sin(($lat1-$lat2)*pi()/180/2),2)+cos($lat1*pi()/180)*cos($lat2*pi()/180)*pow(sin(($lon1-$lon2)*pi()/180/2),2)));
        return $dis;
        
    }
    
}

$lat1 = 39.98505;
$lon1 = 116.403873;
$lat2 = 39.98505+0.000910;
$lon2 = 116.403873+0.000910;

$dis = commonModel::distince($lat1, $lon1, $lat2, $lon2);
var_dump($dis);
?>
