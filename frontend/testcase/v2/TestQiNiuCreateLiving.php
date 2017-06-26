<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/16
 * Time: 16:45
 */
namespace frontend\testcase\v2;

use dosamigos\qrcode\formats\vCard;
use Yii;
use yii\web\Controller;

use frontend\business\ApiCommon;
use frontend\business\ClientUtil;
use frontend\testcase\IApiExcute;
use common\models\Client;

use frontend\zhiboapi\v2\ZhiBoQiNiuCreateLiving;
use frontend\zhiboapi\v2\ZhiBoUpdateKey;
use common\components\AESCrypt;
use common\components\UsualFunForStringHelper;
use common\components\UsualFunForNetWorkHelper;
use yii\base\Exception;
use yii\log\Logger;
use linslin\yii2\curl\Curl;
use frontend\testcase\v2\TestApiUserLogin;

class TestQiNiuCreateLiving  implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $user = new TestApiUserLogin();

        if( empty( $user ) && !isset( $user ) ){
            \Yii::getLogger()->log('模拟用户登陆时出现了现有用户已全部登陆完，请重新增加新测试用户>',Logger::LEVEL_ERROR);
            return false;
        }

        $outInfo  = $user->outInfo;

        $token = $outInfo['token'];
        $sign_key = $outInfo['sign_key'];
        $crypt_key = $outInfo['crypt_key'];
        $device_no = $outInfo['device_no'];
        $device_type = $outInfo['device_type'];
        $data = [
                 "unique_no"=>$outInfo['unique_no'],
                 "register_type" => $outInfo['register_type'],
                 "living_title" =>'',
                 "city"          =>'',
                 "longitude"    =>'',
                 "latitude"     =>'',
                 "op_unique_no" =>UsualFunForStringHelper::CreateGUID(),
                 "is_continue"  =>0,
                 "private_status"=>0,
                 "password"       =>"",
                 "living_type"   =>1
        ];

        $rstData = Common::PackagingParams($token,$sign_key,$crypt_key,'qiniu_create_living',$data,$device_no,
            $device_type,'json');
       return true;
    }
}