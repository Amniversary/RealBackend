<?php
include_once "WXBizMsgCrypt.php";

// 第三方发送消息给公众平台
$encodingAesKey = "63n65FMYpIdj2FvUiH7M9rhG0susnRrcKXzZg86h0fK";
$token = "hongbao";
$timeStamp = "1409304349";
$nonce = "xxxxxx";
$appId = "wx25d7fec30752314f";
$text = "<xml><ToUserName><![CDATA[oia2Tj我是中jewbmiOUlr6X-1crbLOvLw]]></ToUserName><FromUserName><![CDATA[gh_7f083739789a]]></FromUserName><CreateTime>1407743423</CreateTime><MsgType><![CDATA[video]]></MsgType><Video><MediaId><![CDATA[eYJ1MbwPRJtOvIEabaxHs7TX2D-HV71s79GUxqdUkjm6Gs2Ed1KF3ulAOA9H1xG0]]></MediaId><Title><![CDATA[testCallBackReplyVideo]]></Title><Description><![CDATA[testCallBackReplyVideo]]></Description></Video></xml>";

/*
        $_GET = [
        'signature' => '6f835991e3b3f48121a41d894912bbb2a21d72f1'
        'timestamp' => '1498522875'
        'nonce' => '475874109'
        'encrypt_type' => 'aes'
        'msg_signature' => '3ab090ddea0809685d9fe70ea4202fc67684a259'
        ]*/
$pc = new WXBizMsgCrypt($token, $encodingAesKey, $appId);
$encryptMsg = '';
$errCode = $pc->encryptMsg($text, $timeStamp, $nonce, $encryptMsg);
if ($errCode == 0) {
	print("加密后: " . $encryptMsg . "\n");
} else {
	print($errCode . "\n");
}

$xml_tree = new DOMDocument();
$xml_tree->loadXML($encryptMsg);
$array_e = $xml_tree->getElementsByTagName('Encrypt');
$array_s = $xml_tree->getElementsByTagName('MsgSignature');
$encrypt = $array_e->item(0)->nodeValue;
$msg_sign = $array_s->item(0)->nodeValue;

$format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
$from_xml = sprintf($format, $encrypt);

// 第三方收到公众号平台发送的消息
$msg = '';
$errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
if ($errCode == 0) {
	print("解密后: " . $msg . "\n");
} else {
	print($errCode . "\n");
}
