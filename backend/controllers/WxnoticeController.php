<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/26
 * Time: 上午9:02
 */

namespace backend\controllers;


use yii\web\Controller;

class WxnoticeController extends Controller
{
    public function actionIndex()
    {
        $data = $_REQUEST;
        $postStr = $GLOBALS['HTTP_RAW_POST_DATA'];
        $encryptMsg = $postStr;

        $xml_tree = new \DOMDocument();
        $xml_tree->loadXML($encryptMsg);

        $ary = $xml_tree->getElementsByTagName('Encrypt');
        $encrypt = $ary->item(0)->nodeValue;

        $msg_sign = $data['msg_signature'];
        $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
        $form_xml = sprintf($format,$encrypt);

        $msg = '';
        $pc = new WXBizMsgCrypt($this->token,$this->key,$this->appid);

        $timeStamp = $data['timestamp'];
        $nonce = $data['nonce'];
        $errCode = $pc->decryptMsg($msg_sign,$timeStamp,$nonce,$form_xml,$msg);
        if($errCode == 0){
            $postObj = simplexml_load_string($msg,"SimpleXMLElement",LIBXML_NOCDATA);
            $data = (array)$postObj;
            return $data;
        }
    }
}