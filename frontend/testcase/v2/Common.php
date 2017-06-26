<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 17:30
 */

namespace frontend\testcase\v2;

use common\components\AESCrypt;
use common\components\UsualFunForStringHelper;
use common\components\UsualFunForNetWorkHelper;
use yii\base\Exception;
use yii\log\Logger;
use linslin\yii2\curl\Curl;

class Common
{
    public static function PackagingParams($token,$sign_key,$crypt_key,$action_name,$data,$device_no,$device_type,$data_type)
    {
        $rand_str = UsualFunForStringHelper::mt_rand_str(32);
        $data =[ 'app_id' => '1119990982',
            'action_name' => $action_name,
            'app_version_inner' => '1',
            'has_data' => '1',
            'data' =>$data,
            'device_no' => $device_no,
            'api_version' => 'v2',
            'device_type' => $device_type,
            'data_type' => $data_type,
        ];
        $cryptManager = new AESCrypt($crypt_key);
        $aesData  = $cryptManager->encrypt( json_encode($data) );
        $time = time();
        $paramstring =  sprintf('rand_str=%s&time=%s&token=%s&data=%s&myyuanparampasssignkey=%s',
            $rand_str,$time,$token,$aesData,$sign_key);
        $token_other = md5($paramstring);
        $postURLData = [
            "rand_str" => $rand_str,
            "time" => $time,
            "token" => $token,
            "data" => $aesData,
            "token_other" => $token_other
        ];
        $postURL = "http://api.mblive.cn/mbapi/response.do";
        $t1 = microtime(true);
        $curlData = UsualFunForNetWorkHelper::HttpsPost($postURL,$postURLData);
        $t2 = microtime(true);
        $decryptData  =  $cryptManager->decrypt( $curlData );
        \Yii::getLogger()->log($action_name.'接口'.$curlData,Logger::LEVEL_ERROR);
        $diff = $t2 - $t1;
        \Yii::getLogger()->log($action_name.'接口，模拟unique_no为:'.$data['data']['unique_no'].'耗时统计'. '接口运行前时间为：'.$t1.'接口运行后时间为'.$t2.'总耗时：'.$diff,Logger::LEVEL_ERROR);

        $rstData = [
            'token' => $token,
            'sign_key' => $sign_key,
            'crypt_key' => $crypt_key,
            'unique_no' => $data['data']['unique_no'],
            'register_type' => $data['data']['register_type'],
            'device_no' => $device_no,
            'device_type' => $device_type,
            'info' => $decryptData
        ];
        return $rstData;
    }
}