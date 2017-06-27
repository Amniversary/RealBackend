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
    public function actionIndex(){
        if(isset($_GET['code'])){
            \Yii::error($_GET['code']);
            echo $_GET['code'];
        }else{
            echo "no code";
        }
    }

    public function actionNotice()
    {
        /*
        $_GET = [
        'signature' => '6f835991e3b3f48121a41d894912bbb2a21d72f1'
        'timestamp' => '1498522875'
        'nonce' => '475874109'
        'encrypt_type' => 'aes'
        'msg_signature' => '3ab090ddea0809685d9fe70ea4202fc67684a259'
        ]*/
        \Yii::error('Notice rules');
        $data = $_REQUEST;
        if(!isset($data)){
            \Yii::error('data is not isset'. var_export($data));
        }else{
            \Yii::error('data is isset'. var_export($data));
        }
        $postStr = $GLOBALS['HTTP_RAW_POST_DATA'];
        $encryptMsg = $postStr;
        \Yii::error($encryptMsg);

        $xml_tree = new \DOMDocument();
        $xml_tree->loadXML($encryptMsg);

        $ary = $xml_tree->getElementsByTagName('Encrypt');
        $encrypt = $ary->item(0)->nodeValue;

        $msg_sign = $data['msg_signature'];
        $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
        $form_xml = sprintf($format,$encrypt);
        $app_id = WxPayConfig::APPID;
        $wx_params = \Yii::$app->params['wechat_params'];
        \Yii::error('appid : '.$app_id . 'wx_params '. var_export($wx_params));
        $msg = '';
        $pc = new WXBizMsgCrypt($wx_params['token'],$wx_params['key'],$app_id);

        $timeStamp = $data['timestamp'];
        $nonce = $data['nonce'];
        $errCode = $pc->decryptMsg($msg_sign,$timeStamp,$nonce,$form_xml,$msg);
        if($errCode == 0){
            $postObj = simplexml_load_string($msg,"SimpleXMLElement",LIBXML_NOCDATA);
            $data = (array)$postObj;
            \Yii::error("backData: ".var_export($data,true));
            return $data;
        }
        echo "Error";
        exit;
    }

}