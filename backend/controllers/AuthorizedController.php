<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/26
 * Time: 下午4:39
 */

namespace backend\controllers;


use common\components\wxpay\lib\WxPayConfig;
use common\components\wxverify\WXBizMsgCrypt;
use yii\web\Controller;

class AuthorizedController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionTest(){
        echo "<pre>";
        $encrypt = 'k8laj0NvJbaUAsLH3VNIMVlSKIUzcoceGXO006ehVP+2PiMgBoNwBPkX9gcXre4o8nyCM0MROGZc67wdW13A0nTE1itdav4KqGqkoQYP/mzPK5/mphylL46U/EioEUvbPro4SpFTLZKX7a5QEqxIpfJsXZJaFJsLClHHTRgQoD/SIX1U4V1pb1bz64EaNCJnCuuioqxWxYx4l7XQ6yDhDPXyWefDdras1UyAnXMpWK2FcZJv8ce9tK1BusFp+DI/r5jt9zNiQnXq2MeJlYY9C3/CozpXWYXGqUh8twBdrLWCr4Fu7FNF4l5VdabSsD6IIQJqVEyWzhA8Ry/8JeD6Y+XTLX1OvYMYvrMxacHmwEFmM4BsyS5X37Qa3vFtbjphItiZvRzCK7/vfN1q5H2tR102ADaMqVCJCOw1XflZENAc2MFEY0lkiX5GqNtF2W5IQ1OoI9OjWmB/anZdO6tdxw==';
        $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
        $form_xml = sprintf($format,$encrypt);
        var_dump('form_xml:   '.$form_xml);
        echo "<br />";
        print_r('encrypt :  '.$encrypt);
    }

    public function actionIndex(){
        echo "<pre>";
        //TODO: 获取$_GET参数
        $data = [
            'signature' => '6737f3efa301eb09d47b3b46ea966709b3bd0db4',
            'timestamp' => '1498543281',
            'nonce' => '1302082950',
            'encrypt_type' => 'aes',
            'msg_signature' => '0547005e08fc300c710d0838f18c4ce4f7a032c1'
        ];
        //TODO: 获取Xml数据信息
        $postStr = "<xml>
    <AppId><![CDATA[wx25d7fec30752314f]]></AppId>
    <Encrypt><![CDATA[4gimhDmi0lwH6dN892u4FYzyYrox3bCeXehW+IRYZ/btgcNUjByw6d37zutmaczQoDtXIcbXeQUUDvOZ7yWO14bB7U4pSFX3hLwkq5DTOTfxXhXGYUv6B/AXstb3IKq+xMST2WUun9hvBpJ0xStDFGeR+AGo2YCCGH8T9XIXgPUYJE0zvLo6cxsRvGPIYekYNpIqIe3wqx8CRk+uNonLBH/IWMhH2bWfavnyjzDa9j5WE8cQZW1i1+h5xNSUAMF+Q6blK4s6GsJYf02Pm4wbSXr1B5xqGkCnakOI78pYZtQJxWfCiYMPhYDc7iPvFGkAeK0jLQZfWVg+gl2sZm8vJ8svBe3l4o7YlKGNz8Brg6s9/ruKspPgejoD6NqMsR0PpQkpKjf5K9baZe+56wf5Csdo9yDnGUBxBIN2/VtcBwmgkThIDY2v0HLTo7a5Ndpi6EEV2qVE7zGDm2/W5tJGGg==]]></Encrypt>
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
            if($errorCode == 0){
                $postObj = simplexml_load_string($decryptMsg,"SimpleXMLElement",LIBXML_NOCDATA);
                $data = (array)$postObj;
                \Yii::error("backData: ".var_export($data,true));
                return $data;
            }
        }
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