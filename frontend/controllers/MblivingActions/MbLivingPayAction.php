<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/4
 * Time: 11:38
 */
namespace frontend\controllers\MblivingActions;


use common\components\DeviceUtil;
use common\components\wxpay\JsApiPay;
use frontend\business\ClientUtil;
use frontend\business\UserWeiXinUtil;
use yii\base\Action;
use yii\log\Logger;

/**
 * 充值绑定
 * Class MbLivingPayAction
 * @package frontend\controllers\MblivingActions
 */
class MbLivingPayAction extends Action
{
    public function run()
    {

        /*if(!DeviceUtil::IsMobileWeixinBrowse())
        {
            \Yii::getLogger()->log('not mobile',Logger::LEVEL_ERROR);
            return false;
        }*/
        if(!isset(\Yii::$app->session['openid']))
        {
            //获取openid
            $wxUtil = new JsApiPay();
            $openid = $wxUtil->GetOpenid();
            \Yii::$app->session['openid'] = $openid;
        }
        $openid = \Yii::$app->session['openid'];
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/mibo/recharge_bind.html';

        $clientInfo = ClientUtil::GetClientPay($openid);
        if(isset($clientInfo))
        {
            \Yii::getLogger()->log('用户信息: '.var_export($clientInfo,true),Logger::LEVEL_ERROR);
            \Yii::$app->session['recharge_user_id'] = $clientInfo->user_id;
            $url = 'http://'.$_SERVER['HTTP_HOST'].'/mibo/recharge_list.html';//充值跳转页
            return $this->controller->redirect($url);
        }

        return $this->controller->redirect($url);
    }
} 