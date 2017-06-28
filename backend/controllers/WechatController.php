<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/28
 * Time: 上午9:05
 */

namespace backend\controllers;


use backend\business\WeChatUtil;
use backend\components\WeChatComponent;
use common\components\WeiXinUtil;
use common\components\wxpay\lib\WxPayConfig;
use common\models\Authorization;
use yii\web\Controller;

class WechatController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionTest()
    {
        echo "ok";
        exit;
    }

    /**
     *  接收微信回调
     */
    public function actionCallback()
    {
        $WeChat = new WeChatComponent();
        $data = $WeChat->decryptMsg;
        \Yii::error('dataInfo:'.var_export($data,true));
        \Yii::error('openid '. $WeChat->openid . '   AppId:' . $WeChat->AppId);


        echo 'success';
        exit;
    }


    public function actionCallbackurl()
    {

        $AppInfo = Authorization::findOne(['appid'=>WxPayConfig::APPID]);

        echo 'success';
    }


    /**
     * 截取微信回调的动态路由
     * @param $rules //请求Url地址 去掉host test：wechat/wx1283196321321/callback
     * @return string  //AppId 微信公众号的原始ID
     */
    private function getRulesAppid($rules)
    {
        $strstr = strstr($rules,"/");
        $strrpos = strtok($strstr,"/");
        return $strrpos;
    }
}