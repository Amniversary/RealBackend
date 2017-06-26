<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/27
 * Time: 18:14
 */

namespace frontend\business;

use Yii;
use yii\web\Controller;
use yii\base\Action;
use yii\db\Query;
use common\components\UsualFunForNetWorkHelper;
use common\components\wxpay\JsApiPay;
use common\models\WxLiveManage;

class WxLiveManageUtil
{
    public static function GetOpenidUnionid(){
        $wxUtil = new JsApiPay();
        $openid = $wxUtil->GetOpenidForUnionId();
        $access_token = $wxUtil->data['access_token'];
        $url = sprintf('https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN',
            $access_token,
            $openid
        );

        $info = UsualFunForNetWorkHelper::HttpGet($url);
        $jsonArr = json_decode($info, TRUE);
        $unionid = $jsonArr['unionid'];
        return ['openid'=>$openid,'unionid'=>$unionid];
    }
}