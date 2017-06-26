<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/13
 * Time: 14:37
 */

namespace frontend\controllers\MblivingActions;


use common\components\DeviceUtil;
use common\components\UsualFunForNetWorkHelper;
use common\components\wxpay\JsApiPay;
use frontend\business\ClientUtil;
use yii\base\Action;
use yii\log\Logger;

/**
 * 提现检测unionid绑定
 * Class WxWithdrawAction
 * @package frontend\controllers\MblivingActions
 */
class WxWithdrawAction extends Action
{
    public function run()
    {
        /*if(!DeviceUtil::IsMobileWeixinBrowse())
        {
            \Yii::getLogger()->log('not mobile',Logger::LEVEL_ERROR);
            return false;
        }*/
        /*
        \Yii::$app->session['draw_openid'] =null;
        exit;*/
        if(!isset(\Yii::$app->session['draw_openid']))
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

            $unionid = $info->unionid;
            \Yii::$app->session['draw_openid'] = $openid;
            \Yii::$app->session['draw_unionid'] = $unionid;
        }
        $unionid = \Yii::$app->session['draw_unionid'];
        \Yii::error('unionid:'. $unionid);

        $url = 'http://'.$_SERVER['HTTP_HOST'].'/mibo/withdraw.html';
        $clientInfo = ClientUtil::GetClientOtherInfo($unionid);
        \Yii::$app->session['draw_user_id'] = $clientInfo->user_id;

        return $this->controller->redirect($url);
    }
} 