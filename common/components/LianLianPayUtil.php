<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/24
 * Time: 13:18
 */

namespace common\components;


use yii\base\Exception;
use yii\log\Logger;

class LianLianPayUtil
{
    private static $account_id = '201601071000671906';//商户号 //201601071000671906   201512071000628721
    private static $url = 'https://yintong.com.cn/queryapi/bankcardbin.htm';
    private static $signRsaKey ='MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCSS/DiwdCf/aZsxxcacDnooGph3d2JOj5GXWi+q3gznZauZjkNP8SKl3J2liP0O6rU/Y/29+IUe+GTMhMOFJuZm1htAtKiu5ekW0GlBMWxf4FPkYlQkPE0FtaoMP3gYfh+OwI+fIRrpW3ySn3mScnc6Z700nU/VYrRkfcSCbSnRwIDAQAB';
        //'MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAMtjVcVuPILEevo1e85USLPFP4oe+YvdAf9ibRVTTf2wytla4HM5aVB2VU04NK76T/RFM6SGKW4k5bfr0iDQHhy1h3XK3A5KZXn2/0SgG1nXF35Z/2ygwsBJB9u4Aplv5yXSAkDca1b07+R5C0MQ9T/qxl3u7qXxOZbuDQiZen7RAgMBAAECgYBwYW7fLE4bI12gKzVBiKizTGYTd5IDiha0ejoz8lfBuZIcD269vBdI8lmn1Uqm9ICBREuIuOjjh1e3DIKKxvQbhLV23G9vNh+4KRq8XFmq+S5UrjdXkdCQBTwAAKtanq0rVVvY7fQqqmBVoio7ppJSTIWAF0HzccRvwN7L2zzlUQJBAOY66D17XRpza7kmyHGkmUzaCcr6yfVAhEA04TLrB96FI9BnyZhYTd8Byb5WAlSl8gjWLPWA3/wEyVoLV3SKPdcCQQDiJ0kicVJx+2ZD0fFJbTor5UzEjEzd161qjW8312H9dmkIew+r0XufHQR0+cHnRz0mdQIkB3kSeYkxYivMrYOXAkEAox8loTog0zboIj0qU+qNe3gY7CoYoZ3elidhT9RatPycTXLb0Qbv1YvMxwDlkdgpzr0BCckP6d3yU8wpYLb57QJBAKbZMxTNJHVhc7ZviqOQiU1fU77if8df2tp01GwPZIUaOi1+lTY/TAQ85U/kABHAtjXMN7MbLFDcB8K+WfAcx6sCQFnBs67a19YloYGFwxE9DWjoJB5BzjiEs9i4mYqJk4kHhQbmk3qTq1QC7fu+nAgcdIudPzXSKZNnws5LE+3F1Ps=';
        //'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCSS/DiwdCf/aZsxxcacDnooGph3d2JOj5GXWi+q3gznZauZjkNP8SKl3J2liP0O6rU/Y/29+IUe+GTMhMOFJuZm1htAtKiu5ekW0GlBMWxf4FPkYlQkPE0FtaoMP3gYfh+OwI+fIRrpW3ySn3mScnc6Z700nU/VYrRkfcSCbSnRwIDAQAB';
    private static $signMd5Key = 'gsklgoqSAKKJHASFJKGWEAHAKFHDHDS';//gsklgoqSAKKJHASFJKGWEAHAKFHDHDS    SJMxueXinbao11529223
    /**
     * @param $card_no
     * @param $outRst
     * @param $error
     * @return bool
     */
    public static function CheckBankCardOk($card_no,&$outRst,&$error)
    {
        $params=[
            'api_version'=>'1.0',
            'oid_partner'=>self::$account_id,
            'sign_type'=>'MD5',
            'card_no'=>$card_no,
        ];
        $sign = self::GetMd5Sign($params);
        $params['sign'] = $sign;
        $str = json_encode($params);
        $rst = UsualFunForNetWorkHelper::HttpsPost(self::$url, $str);
        if(empty($rst))
        {
            $error = '网络异常，没有返回值';
            return false;
        }
        $rst = json_decode($rst);
        if(!is_object($rst) || !isset($rst->ret_code) || $rst->ret_code !== '0000')
        {
            //\Yii::getLogger()->log(var_export($rst,true), Logger::LEVEL_ERROR);
            if(isset($rst->ret_msg))
            {
                $error = '检测银行卡未通过，'.$rst->ret_msg;
            }
            else
            {
                $error = '检测银行卡结果异常';
            }
            return false;
        }
        $outRst = [
            'bank_name'=>$rst->bank_name,
            'card_type'=>self::ChangeCardType($rst->card_type)
        ];
        return true;
    }

    /**
     * 转换卡类型
     * @param $card_type
     * @return string
     */
    public static function ChangeCardType($card_type)
    {
        switch($card_type)
        {
            case '2':
                return '1';
            case '3':
                return '2';
            default:
                return '0';
        }
    }

    /**RSA签名
     * $data签名数据(需要先排序，然后拼接)
     * 签名用商户私钥，必须是没有经过pkcs8转换的私钥
     * 最后的签名，需要用base64编码
     * return Sign签名
     */
   public static function Rsasign($data,$priKey)
   {
        //转换为openssl密钥，必须是没有经过pkcs8转换的私钥
        $res = openssl_get_privatekey($priKey);

        //调用openssl内置签名方法，生成签名$sign
        openssl_sign($data, $sign, $res,OPENSSL_ALGO_MD5);

        //释放资源
        openssl_free_key($res);

        //base64编码
        $sign = base64_encode($sign);
        //file_put_contents("log.txt","签名原串:".$data."\n", FILE_APPEND);
        return $sign;
   }


    /**RSA验签
     * $data待签名数据(需要先排序，然后拼接)
     * $sign需要验签的签名,需要base64_decode解码
     * 验签用连连支付公钥
     * return 验签是否通过 bool值
     */
    public  function Rsaverify($data, $sign)
    {
        //读取连连支付公钥文件
        $pubKey = file_get_contents('key/llpay_public_key.pem');

        //转换为openssl格式密钥
        $res = openssl_get_publickey($pubKey);

        //调用openssl内置方法验签，返回bool值
        try
        {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res,OPENSSL_ALGO_MD5);
        }
        catch(Exception $e)
        {
            throw $e;
        }


        //释放资源
        openssl_free_key($res);

        //返回资源是否成功
        return $result;
    }

    /**
     * 获取rsa签名字符串
     * @param $params
     * @return string
     */
    public static function GetRsaSign($params)
    {
        ksort($params);
        $signSourceStr = '';
        foreach($params as $key => $value)
        {
            $signSourceStr .= $key.'='.$value.'&';
        }
        $signSourceStr = substr($signSourceStr,0,strlen($signSourceStr) -1);
        return self::Rsasign($signSourceStr,self::$signRsaKey);
    }

    /**
     * 获取md5签名
     * @param $params
     * @return string
     */
    public static function GetMd5Sign($params)
    {
        ksort($params);
        $signSourceStr = '';
        foreach($params as $key => $value)
        {
            $signSourceStr .= $key.'='.$value.'&';
        }
        $signSourceStr .= 'key='.self::$signMd5Key;
        return md5($signSourceStr);
    }
} 