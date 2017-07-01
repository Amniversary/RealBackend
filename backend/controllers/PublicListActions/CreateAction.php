<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/29
 * Time: 下午4:20
 */

namespace backend\controllers\PublicListActions;


use backend\business\WeChatUtil;
use backend\components\ExitUtil;
use common\components\wxpay\lib\WxPayConfig;
use yii\base\Action;

class CreateAction extends Action
{
    public function run()
    {
        $WeChat =new WeChatUtil();
        if(!$WeChat->getAuthCode($rst,$error)){
            ExitUtil::ExitWithMessage('获取预授权码失败');
        }
        $url = sprintf('https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s',
            WxPayConfig::APPID,
            $rst['pre_auth_code'],
            'http://wxmp.gatao.cn/wechat/callbackurl' //wechat/callbackurl
        );
        //echo '<a href="'.$url .'">点击授权</a>';
        return $this->controller->redirect($url);
    }
}