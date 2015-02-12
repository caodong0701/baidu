<?php
/**
 * Description of encodeModel
 *
 * @author caodong
 */

class encodeModel {
    
    private static $key = "keyasd";
    public static function passport_encrypt($txt) {
        $key = self::$key;
        srand((double) microtime() * 1000000);
        $encrypt_key = md5(rand(0, 32000));
        $ctr = 0;
        $tmp = '';
        for ($i = 0; $i < strlen($txt); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $encrypt_key[$ctr] . ($txt[$i] ^ $encrypt_key[$ctr++]);
        }
        return base64_encode(self::passport_key($tmp, $key));
    }

    public static function passport_decrypt($txt) {
        $key = self::$key;
        $txt = self::passport_key(base64_decode($txt), $key);
        $tmp = '';
        for ($i = 0; $i < strlen($txt); $i++) {
            $md5 = $txt[$i];
            $tmp .= $txt[++$i] ^ $md5;
        }
        return $tmp;
    }

    public static function passport_key($txt, $encrypt_key) {
        $encrypt_key = md5($encrypt_key);
        $ctr = 0;
        $tmp = '';
        for ($i = 0; $i < strlen($txt); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
        }
        return $tmp;
    }

}

$id = "1000";
$eid = encodeModel::passport_encrypt($id);
var_dump($eid);
$id = "1";
echo "{$id}<br />";
echo md5($id)."<br />";
echo sha1($id)."<br />";
echo crc32($id)."<br />";

printf("%u",crc32($id));

?>
