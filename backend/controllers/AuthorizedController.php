<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/26
 * Time: 下午4:39
 */

namespace backend\controllers;


use backend\components\WeChatComponent;
use common\components\wxpay\lib\WxPayConfig;
use common\models\Authorization;
use yii\web\Controller;

class AuthorizedController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * 测试公众号授权接口
     */
    public function actionTest(){

        $AppInfo = Authorization::findOne(['app_id'=>WxPayConfig::APPID]);
        $url = sprintf('https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s',
            $AppInfo->app_id,
            $AppInfo->pre_auth_code,
            'http://wxmp.gatao.cn/wechat/callbackurl' //wechat/callbackurl
            );

        echo '<a href="'.$url .'">点击授权</a>';

    }

    /**
     * 解密回调URl中XML的加密信息
     * @return string
     */
    public function actionNotice()
    {
        $WeChat = new WeChatComponent();
        $record = Authorization::findOne(['app_id'=>$WeChat->webAppId]);
        $data = $WeChat->decryptMsg;

        $infoType = isset($data['InfoType']) ? $data['InfoType']: '';
        if(!empty($infoType) && $infoType == 'unauthorized'){
            $authorzer_appid = $data['AuthorizerAppid'];

            return 'success';
        }
        //TODO: 保存数据到数据库
        if($record){
            $record->create_time = $data['CreateTime'];
            $record->verify_ticket = $data['ComponentVerifyTicket'];
        }else{
            $record = new Authorization();
            $record->app_id = $data['AppId'];
            $record->create_time = $data['CreateTime'];
            $record->verify_ticket = $data['ComponentVerifyTicket'];
        }
        if(!$record->save()){
            \Yii::error('保存授权码Ticket失败 ：'.var_export($record->getErrors(),true));
        }
        return 'success';
    }

}
/*
        $_GET = [
        'signature' => '6f835991e3b3f48121a41d894912bbb2a21d72f1'
        'timestamp' => '1498522875'
        'nonce' => '475874109'
        'encrypt_type' => 'aes'
        'msg_signature' => '3ab090ddea0809685d9fe70ea4202fc67684a259'
        ]*/

/*
<xml>
    <AppId><![CDATA[wx25d7fec30752314f]]></AppId>
    <Encrypt><![CDATA[k8laj0NvJbaUAsLH3VNIMVlSKIUzcoceGXO006ehVP+2PiMgBoNwBPkX9gcXre4o8nyCM0M
                      ROGZc67wdW13A0nTE1itdav4KqGqkoQYP/mzPK5/mphylL46U/EioEUvbPro4SpFTLZKX7a5
                      QEqxIpfJsXZJaFJsLClHHTRgQoD/SIX1U4V1pb1bz64EaNCJnCuuioqxWxYx4l7XQ6yDhDPX
                      yWefDdras1UyAnXMpWK2FcZJv8ce9tK1BusFp+DI/r5jt9zNiQnXq2MeJlYY9C3/CozpXWYX
                      GqUh8twBdrLWCr4Fu7FNF4l5VdabSsD6IIQJqVEyWzhA8Ry/8JeD6Y+XTLX1OvYMYvrMxacH
                      mwEFmM4BsyS5X37Qa3vFtbjphItiZvRzCK7/vfN1q5H2tR102ADaMqVCJCOw1XflZENAc2MFE
                      Y0lkiX5GqNtF2W5IQ1OoI9OjWmB/anZdO6tdxw==]]>
    </Encrypt>
</xml>*/