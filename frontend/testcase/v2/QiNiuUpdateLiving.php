<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 20:01
 */

namespace frontend\testcase\v2;

use Yii;
use common\components\UsualFunForStringHelper;

/*
 * 更新直播
 */
class QiNiuUpdateLiving
{
    public static function UpdateLiving($outInfo,$living_id,$group_id){
        $token = $outInfo['token'];
        $sign_key = $outInfo['sign_key'];
        $crypt_key = $outInfo['crypt_key'];
        $device_no = $outInfo['device_no'];
        $device_type = $outInfo['device_type'];
        $data = [
            "unique_no"=>$outInfo['unique_no'],
            "register_type" => $outInfo['register_type'],
            "living_id" =>$living_id,
            "group_id"  =>$group_id
        ];

        $rstData = Common::PackagingParams(
            $token,
            $sign_key,
            $crypt_key,
            'qiniu_update_living',
            $data,$device_no,
            $device_type,
            'json'
        );

        return $rstData;
    }
}