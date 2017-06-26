<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午6:59
 */

namespace common\components;


class Des3Crypt
{
    //des加密函数  密钥必须192bit=24byte
    public static function des_encrypt($encrypt, $key = "")
    {

        //按pkcs7 处理块
        $block = mcrypt_get_block_size('tripledes', 'cbc');
        $len = strlen($encrypt);
        $padding = $block - ($len % $block);
        $encrypt .= str_repeat(chr($padding),$padding);

        $a = array(107, 115, 101, 110, 115, 101, 119, 97);
        //$iv = mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC ) );
        for ($i = 0; $i < 8; $i++)
            $iv .= chr($a[$i]);
        $passcrypt = mcrypt_encrypt(MCRYPT_3DES, $key, $encrypt, MCRYPT_MODE_CBC, $iv);

        //for($i = 0; $i < )
        $encode = base64_encode($passcrypt);
        return $encode;
    }

    //des解密函数：decrypt 密钥必须192bit=24byte
    public static function des_decrypt($decrypt, $key = "")
    {
        $a = array(107, 115, 101, 110, 115, 101, 119, 97);
        $decoded = base64_decode($decrypt);
        //$iv = mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_64, MCRYPT_MODE_CBC ) );
        for ($i = 0; $i < 8; $i++)
            $iv .= chr($a[$i]);
        $decrypted = mcrypt_decrypt(MCRYPT_3DES, $key, $decoded, MCRYPT_MODE_CBC, $iv);

        //按pkcs7处理块
        $block = mcrypt_get_block_size('tripledes', 'cbc');
        $packing = ord($decrypted[strlen($decrypted) - 1]);
        //再linux下，刚好匹配时有可能会填充8个8.所有条件加等号
        if(!empty($packing) and ($packing <= $block))//如果有填充字符串，去除填充字符串
        {
            $decrypted = substr($decrypted,0,strlen($decrypted) - $packing);
        }

        return $decrypted;
    }
} 