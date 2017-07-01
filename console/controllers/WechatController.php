<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/27
 * Time: 下午4:30
 */

namespace console\controllers;


use backend\business\WeChatUtil;
use yii\console\Controller;

class WechatController extends Controller
{
    /**
     * 定时获取微信授权Token
     */
    public function actionGettoken()
    {
        $wechat = new WeChatUtil();
        if(!$wechat->getToken($error)){
            echo "$error \n";
            exit;
        }
        $time = date('Y-m-d H:i:s');

        echo "get Token success time:$time \n";
    }

    /**
     * 刷新用户授权公众号access_token
     */
    public function actionRefreshauthtoken()
    {
        $wechat = new WeChatUtil();
        $wechat->refreshAuthToken();
    }
}