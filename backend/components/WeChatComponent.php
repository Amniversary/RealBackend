<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/28
 * Time: 下午12:27
 */

namespace backend\components;


use common\components\wxpay\lib\WxPayConfig;
use yii\base\Exception;
use yii\base\Object;

class WeChatComponent extends Object
{
    public $webAppId;    //TODO：AppID
    public $appSecret;   //TODO：AppSecret
    public $encryptKey;  //TODO：加密Key(EncryptKey)
    public $token;       //TODO：公众平台自定义Token
    public $encryptMsg;  //TODO: 加密Xml
    public $decryptMsg;  //TODO: 解密串
    public $encryptType; //TODO: 加密类型
    public $nonce;
    public $timestamp;
    public $signature;
    public $msgSignature;
    public $errorCode;
    public $errorMsg;
    public $openid;
    public $AppId;
    public $MsgType;


    /**
     * 初始化微信配置参数
     */
    public function init()
    {
        //TODO: 获取$_GET参数
        $data = $_REQUEST;
        $this->webAppId = WxPayConfig::APPID;
        $this->appSecret = WxPayConfig::APPSECRET;
        $this->encryptKey = WxPayConfig::ENCRYPT_KEY;
        $this->token = WxPayConfig::TOKEN;
        //TODO: 获取Xml数据信息
        $this->encryptMsg = file_get_contents("php://input");
        $this->AppId = \Yii::$app->request->get('appid','');
        $this->openid = !empty($data['openid'])? $data['openid'] : '';
        $this->encryptType = !empty($data['encrypt_type'])? $data['encrypt_type'] : '';
        $this->nonce = !empty($data['nonce'])? $data['nonce'] : '';
        $this->timestamp = !empty($data['timestamp'])? $data['timestamp'] : '';
        $this->signature = !empty($data['signature'])? $data['signature'] : '';
        $this->msgSignature = !empty($data['msg_signature'])? $data['msg_signature'] : '';

        if(!empty($this->encryptType)){
            $encrypt = $this->XmlToArr($this->encryptMsg)['Encrypt'];
            $this->decryptMsg = $this->decryptArr($encrypt);
        } else{
            $dataArr = $this->XmlToArr($this->encryptMsg);
            $this->decryptMsg = json_decode(json_encode($dataArr),true);
        }
        $this->MsgType = isset($this->decryptMsg['MsgType'])?$this->decryptMsg['MsgType'] : '';

        parent::init(); // TODO: Change the autogenerated stub
    }


    /**
     * 验证签名 将密文解密成数组返回
     * @param $encrypt
     * @return bool
     */
    public function decryptArr($encrypt)
    {
        if(strlen($this->encryptKey) != 43){
            $this->errorCode = 40004;
            return false;
        }
        if ($this->timestamp == null) $this->timestamp = time();
        $sign = $this->VerifySha1($encrypt);
        if($sign != $this->msgSignature){
            $this->errorCode = 40001;
            return false;
        }
        if (!$this->decrypt($encrypt,$rst)) return false;

        return $rst;
    }


    /**
     * 解密密文
     * @param $encrypted
     * @param $rst
     * @return bool
     */
    public function decrypt($encrypted,&$rst)
    {
        $key = base64_decode($this->encryptKey . "=");
        try {
            //TODO: 使用BASE64对需要解密的字符串进行解码
            $ciphertext_dec = base64_decode($encrypted);
            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            $iv = substr($key, 0, 16);
            mcrypt_generic_init($module, $key, $iv);
            //TODO: 解密
            $decrypted = mdecrypt_generic($module, $ciphertext_dec);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
        } catch (Exception $e) {
            $this->errorCode = 40007;
            return false;
        }

        try {
            //TODO: 去除补位字符
            $result = $this->decode($decrypted);
            //TODO: 去除16位随机字符串,网络字节序和AppId
            if (strlen($result) < 16) return "";
            $content = substr($result, 16, strlen($result));
            $len_list = unpack("N", substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_appid = substr($content, $xml_len + 4);
        } catch (Exception $e) {
            $this->errorCode = 40008;
            return false;
        }
        if ($from_appid != $this->webAppId){
            $this->errorCode = 40005;
            return false;
        }
        $rst = $this->XmlToArr($xml_content);
        return true;
    }

    /**
     * XML格式转数组格式
     * @param $xml
     * @return array
     */
    public function XmlToArr($xml)
    {
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $arr;
    }

    /**
     * 用SHA1算法生成安全签名
     * @param string $token 票据
     * @param string $timestamp 时间戳
     * @param string $nonce 随机字符串
     * @param string $encrypt 密文消息
     * @return bool
     */
    public function VerifySha1($encrypt)
    {
        $array = [$encrypt, $this->token, $this->timestamp, $this->nonce];
        sort($array, SORT_STRING);
        $str = implode($array);
        $sign = sha1($str);
        return $sign;
    }

    /**
     * 对解密后的明文进行补位删除
     * @param decrypted //解密后的明文
     * @return bool|string //删除填充补位后的明文
     */
    public function decode($text)
    {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > 32) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }


    /**
     * 返回对应错误码信息
     * @param $errCode
     * @return string
     */
    public function getErrorMsg($errCode)
    {
        $config = \Yii::$app->getBasePath().'components/WeChatErrorCode.php';
        if(!file_exists($config)) return "找不到对应配置文件 WeChatErrorCode";
        $errMsg = require($config);
        return $errMsg[$errCode];
    }


    /**
     * 将公众平台回复用户的消息加密打包.
     * <ol>
     *    <li>对要发送的消息进行AES-CBC加密</li>
     *    <li>生成安全签名</li>
     *    <li>将消息密文和安全签名打包成xml格式</li>
     * </ol>
     *
     * @param $replyMsg string 公众平台待回复用户的消息，xml格式的字符串
     * @param $timeStamp string 时间戳，可以自己生成，也可以用URL参数的timestamp
     * @param $nonce string 随机串，可以自己生成，也可以用URL参数的nonce
     * @param &$encryptMsg string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp, nonce, encrypt的xml格式的字符串,
     *                      当return返回0时有效
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function encryptMsg($replyMsg, &$encryptMsg)
    {
        $encrypt = $this->encrypt($replyMsg, $this->webAppId);
        if(!$encrypt) return false;
        $signature = $this->VerifySha1($encrypt);
        //生成发送的xml
        $encryptMsg = $this->generate($encrypt, $signature);
        return true;
    }
    /**
     * 对明文进行加密
     * @param string $text 需要加密的明文
     * @param $appid //appId
     * @return array|bool 加密后的密文
     */
    public function encrypt($text, $appid)
    {
        $key = base64_decode($this->encryptKey . "=");
        try {
            //TODO: 获得16位随机字符串，填充到明文之前
            $random = $this->getRandomStr();
            $text = $random . pack("N", strlen($text)) . $text . $appid;
            //TODO: 网络字节序

            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            $iv = substr($key, 0, 16);
            //TODO: 使用自定义的填充方式对明文进行补位填充
            $text = $this->encode($text);
            mcrypt_generic_init($module, $key, $iv);
            //TODO: 加密
            $encrypted = mcrypt_generic($module, $text);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
            $rst = base64_encode($encrypted);
            //TODO: 使用BASE64对加密后的字符串进行编码
            return $rst;
        } catch (Exception $e) {
            $this->errorCode = 40006;
            return false;
        }
    }

    /**
     * 对需要加密的明文进行填充补位
     * @param $text  //需要进行填充补位操作的明文
     * @return string 补齐明文字符串
     */
    public function encode($text)
    {
        $block_size = 32;
        $text_length = strlen($text);
        //计算需要填充的位数
        $amount_to_pad = $block_size - ($text_length % $block_size);
        if ($amount_to_pad == 0) {
            $amount_to_pad = $block_size;
        }
        //获得补位所用的字符
        $pad_chr = chr($amount_to_pad);
        $tmp = "";
        for ($index = 0; $index < $amount_to_pad; $index++) {
            $tmp .= $pad_chr;
        }
        return $text . $tmp;
    }

    /**
     * 随机生成16位字符串
     * @return string 生成的字符串
     */
    public function getRandomStr()
    {
        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }

    /**
     * 生成加密Xml格式
     * @param $encrypt  //加密串
     * @param $signature  //签名
     * @return string
     */
    public function generate($encrypt,$signature)
    {
        $format = "<xml>
                        <Encrypt><![CDATA[%s]]></Encrypt>
                        <MsgSignature><![CDATA[%s]]></MsgSignature>
                        <TimeStamp>%s</TimeStamp>
                        <Nonce><![CDATA[%s]]></Nonce>
                    </xml>";
        return sprintf($format, $encrypt, $signature, $this->timestamp, $this->nonce);
    }
}