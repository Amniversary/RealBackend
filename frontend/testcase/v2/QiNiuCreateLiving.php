<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/16
 * Time: 16:45
 */
namespace frontend\testcase\v2;

use Yii;
use common\components\UsualFunForStringHelper;
use Faker\Provider\Uuid;
/*
 * 创建直播
 */
class QiNiuCreateLiving
{
    public static function CreateLiving($outInfo){
        $token = $outInfo['token'];
        $sign_key = $outInfo['sign_key'];
        $crypt_key = $outInfo['crypt_key'];
        $device_no = $outInfo['device_no'];
        $device_type = $outInfo['device_type'];
        $data = [
            "unique_no" => $outInfo['unique_no'],
            "register_type" => $outInfo['register_type'],
            "living_title" => '',
            "city" => '',
            "longitude" => '',
            "latitude" => '',
            "op_unique_no" => Uuid::uuid(),
            "is_continue" => 0,
            "private_status" => 0,
            "password" => "",
            "living_type" => 1
        ];

        $rstData = Common::PackagingParams(
            $token,
            $sign_key,
            $crypt_key,
            'qiniu_create_living',
            $data,$device_no,
            $device_type,
            'json'
        );

        return $rstData;
    }

}