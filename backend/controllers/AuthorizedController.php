<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/26
 * Time: 下午4:39
 */

namespace backend\controllers;


use backend\business\WeChatUtil;
use common\components\wxpay\lib\WxPayConfig;
use common\components\wxverify\WXBizMsgCrypt;
use common\models\Authorization;
use yii\web\Controller;

class AuthorizedController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionTest(){

        $AppInfo = Authorization::findOne(['app_id'=>WxPayConfig::APPID]);
        $url = sprintf('https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s',
            $AppInfo->app_id,
            $AppInfo->pre_auth_code,
            'wxmp.gatao.cn\wxnotice\cellback'
            );

        echo '<a href="'.$url .'">点击授权</a>';

    }

    public function actionIndex(){
        echo "<pre>";
        //TODO: 获取$_GET参数
        $data = [
            'signature' => '6554ee618d08ba93818e7858111c366c41445ad1',
            'timestamp' => '1498546880',
            'nonce' => '906464788',
            'encrypt_type' => 'aes',
            'msg_signature' => 'f3c5e6319a31a3b830e0eaa62fb1939c06161334',
        ];
        //TODO: 获取Xml数据信息
        $postStr = "<xml>
    <AppId><![CDATA[wx25d7fec30752314f]]></AppId>
    <Encrypt><![CDATA[xnRhwluCZmHuQpXv7yR/sXYVWuiDDaCqesSPdH5HLsZLGdec86KsiDfoBca/Qsuehk4AYJZgL30vavcJHUdBH928cYXzyG691Y0NY04wFx6yimn7MnHih2Wp61oWbLGPV0H5HdzKtOf3EvVdSosrPkYoVLg6R88oAT8Gpgh6fLQuqhNQI/yGRks1G/2EOfJ95z8FX76iKkQRY/lShUEPPz2C08MslWNy1AO4VrtPMyGGKqL3rIlViZsaVk0NOCoW6L84LaKT/MW1YhlblRgpuum8+k8UAc6wRXQv7mRNcl3JizYLab1VW0MtUZ1UD+ApWgF6NP1n465uU6t7HRFR8xDcWRqYiFPnVVFcmkXgove4pzXdg75seM/noeX0ETyJqqnYibfDvlTx7iKriJPBJjBSp+ViSuqmH428MshdIG4IKJgLZFM6QgrimVK5NlGqy9hQCKYdYMukDxLM1HHuVA==]]></Encrypt>
</xml>";
        if(!empty($postStr)){
            $encryptMsg = $postStr;
            //TODO:  判断加密类型
            if($data['encrypt_type'] == 'aes'){
                $xml_tree = new \DOMDocument();
                $xml_tree->loadXML($encryptMsg);
                $ary = $xml_tree->getElementsByTagName('Encrypt');
                //TODO: 获取Xml里加密信息encrypt
                $encrypt = $ary->item(0)->nodeValue;
                $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
                $form_xml = sprintf($format,$encrypt);
                $app_id = WxPayConfig::APPID;
                $wx_params = \Yii::$app->params['wechat_params'];
                $pc = new WXBizMsgCrypt($wx_params['token'],$wx_params['key'],$app_id);
                $decryptMsg = '';
                //TODO: 解密Xml内容
                $errorCode = $pc->decryptMsg($data['msg_signature'],$data['timestamp'],$data['nonce'],$form_xml,$decryptMsg);
                print_r('code : '.$errorCode);
                echo "<br />";
                if($errorCode == 0){
                    $postObj = simplexml_load_string($decryptMsg,"SimpleXMLElement",LIBXML_NOCDATA);
                    $data = (array)$postObj;
                    print_r($data);
                    exit;
                }
            }
        }else{
            echo "no postStr";
            exit;
        }
    }

    /**
     * 解密回调URl中XML的加密信息
     * @return array
     */
    public function actionNotice()
    {
        //TODO: 获取$_GET参数
        $data = $_REQUEST;
        if(!isset($data)){
            \Yii::error('data is not isset: '. var_export($data,true));
            exit;
        }
        //TODO: 获取Xml数据信息
        $postStr = file_get_contents("php://input");
        if(empty($postStr)){
            echo "Not Xml";
            exit;
        }
        $encryptMsg = $postStr;
        \Yii::error('Data: '.var_export($data,true));
        \Yii::error('encrypt : '. $encryptMsg);
        //TODO:  判断加密类型
        if($data['encrypt_type'] == 'aes'){
            $xml_tree = new \DOMDocument();
            $xml_tree->loadXML($encryptMsg);
            $ary = $xml_tree->getElementsByTagName('Encrypt');
            //TODO: 获取Xml里加密信息encrypt
            $encrypt = $ary->item(0)->nodeValue;
            //TODO: 转换成可用的Xml格式
            $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
            $form_xml = sprintf($format,$encrypt);
            $app_id = WxPayConfig::APPID;
            $wx_params = \Yii::$app->params['wechat_params'];
            $pc = new WXBizMsgCrypt($wx_params['token'],$wx_params['key'],$app_id);
            $decryptMsg = '';
            //TODO: 解密Xml内容
            $errorCode = $pc->decryptMsg($data['msg_signature'],$data['timestamp'],$data['nonce'],$form_xml,$decryptMsg);
            \Yii::error('code : '.$errorCode);
            if($errorCode == 0){
                $postObj = simplexml_load_string($decryptMsg,"SimpleXMLElement",LIBXML_NOCDATA);
                $data = (array)$postObj;
                \Yii::error("backData: ".var_export($data,true));
                $record = Authorization::findOne(['app_id'=>$data['AppId']]);
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
                echo "success";
            }
        }
    }

    public function actionGettoken()
    {
        $wechat = new WeChatUtil(WxPayConfig::APPID,WxPayConfig::APPSECRET);
        if(!$wechat->getAuthCode($error)){
            print_r($error);
            exit;
        }
        echo "ok";
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