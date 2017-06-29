<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/28
 * Time: 上午9:05
 */

namespace backend\controllers;


use backend\business\WeChatUtil;
use backend\components\ExitUtil;
use backend\components\WeChatComponent;
use yii\web\Controller;

class WechatController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionTest()
    {

    }

    /**
     *  接收微信事件回调
     */
    public function actionCallback()
    {
       /* data Array
        (
            [ToUserName] => gh_364f29031c56
            [FromUserName] => ou0ZXv5uSoetzq_FeIjXYXrpOY_4
            [CreateTime] => 1498640826
            [MsgType] => event
            [Event] => VIEW
            [EventKey] => http://www.cswanda.com/movie/play.html
            [MenuId] => 414144564
        )*/
        echo "<pre>";
        $WeChat = new WeChatComponent();
        $data = $WeChat->decryptMsg;
        \Yii::error('data:'.var_export($data,true));

        return 'success';
    }

    /**
     * 微信公众号授权成功回调接口
     * @return \yii\web\Response
     */
    public function actionCallbackurl()
    {
        $data = $_REQUEST;
        if(empty($data['auth_code'])){
            \Yii::error('auth_code is empty :' . var_export($data,true));
            ExitUtil::ExitWithMessage('获取auth_code失败，auth_code为空');
        }
        $WeChat = new WeChatUtil();
        //TODO: 获取授权公众号的授权数据
        if(!$WeChat->getQueryAuth($data['auth_code'],$res,$error)){
            ExitUtil::ExitWithMessage($error);
        }
        $AuthInfo = $res['authorization_info'];
        //TODO: 获取授权人帐号基本信息和公众号的基本信息
        if(!$WeChat->getAuthorizeInfo($AuthInfo['authorizer_appid'],$outInfo,$error)){
            ExitUtil::ExitWithMessage($error);
        }
        $authorizer_info = $outInfo['authorizer_info'];
        //TODO: 保存授权数据
        if(!$WeChat->SaveAuthInfo($AuthInfo,$authorizer_info,$error)){
            ExitUtil::ExitWithMessage($error);
        }
        return $this->redirect(['site/index']);
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