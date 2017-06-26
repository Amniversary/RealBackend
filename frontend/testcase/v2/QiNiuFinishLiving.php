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

/*
 * 完成直播
 */
class QiNiuFinishLiving
{
    public static function FinishLiving ($outInfo,$living_id){
        $token = $outInfo['token'];
        $sign_key = $outInfo['sign_key'];
        $crypt_key = $outInfo['crypt_key'];
        $device_no = $outInfo['device_no'];
        $device_type = $outInfo['device_type'];
        $data = [
            "unique_no"=>$outInfo['unique_no'],
            "register_type" => $outInfo['register_type'],
            "living_id"    =>$living_id
        ];

        $rstData = Common::PackagingParams($token,$sign_key,$crypt_key,'finish_living',$data,$device_no,
            $device_type,'json');

        return $rstData;
    }

}