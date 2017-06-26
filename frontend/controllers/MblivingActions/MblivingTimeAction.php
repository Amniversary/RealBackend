<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/29
 * Time: 13:13
 */

namespace frontend\controllers\MblivingActions;


use common\components\UsualFunForNetWorkHelper;
use common\components\wxpay\JsApiPay;
use frontend\business\ClientUtil;
use yii\base\Action;
use yii\log\Logger;

class MblivingTimeAction extends Action
{

    public function run ()
    {
        if(!isset(\Yii::$app->session['time_openid']))
        {
            //获取openid
            $wxUtil = new JsApiPay();
            $openid = $wxUtil->GetOpenidForUnionId();
            $access_token = $wxUtil->data['access_token'];
            $url  = sprintf('https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN',
                $access_token,
                $openid
            );
            $info = UsualFunForNetWorkHelper::HttpGet($url);
            $info = json_decode($info);
            //\Yii::getLogger()->log('url:'.$url,Logger::LEVEL_ERROR);
            //\Yii::getLogger()->log('info:'.var_export($info,true),Logger::LEVEL_ERROR);
            $unionid = $info->unionid;
            \Yii::$app->session['time_openid'] = $openid;
            \Yii::$app->session['time_unionid'] = $unionid;

        }

        $unionid = \Yii::$app->session['time_unionid'];

        \Yii::getLogger()->log('unionid:'. $unionid,Logger::LEVEL_ERROR );

        $url = 'http://'.$_SERVER['HTTP_HOST'].'/mibo/livinginfo.html';

        $clientInfo = ClientUtil::GetClientOtherInfo($unionid);

        //\Yii::getLogger()->log('用户信息: '.var_export($clientInfo,true),Logger::LEVEL_ERROR);
        \Yii::$app->session['living_user_id'] = $clientInfo->user_id;

        return $this->controller->redirect($url);
    }
} 