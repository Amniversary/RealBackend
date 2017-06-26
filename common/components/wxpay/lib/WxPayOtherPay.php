<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/2/23
 * Time: 14:28
 */

namespace common\components\wxpay\lib;


class WxPayOtherPay extends WxPayDataBase
{
    /**
     * app端的appid
     * @param string $value
     **/
    public function SetAppid($value)
    {
        $this->values['appid'] = $value;
    }
    /**
     * 获取微信分配的公众账号ID的值
     * @return 值
     **/
    public function GetAppid()
    {
        return $this->values['appid'];
    }

    /**
     * 微信支付商户号
     * @param string $value
     **/
    public function SetPartnerid($value)
    {
        $this->values['partnerid'] = $value;
    }
    /**
     * 获取微信支付商户号
     * @return 值
     **/
    public function GetPartnerid()
    {
        return $this->values['partnerid'];
    }

    /**
     * 设置支付交易会话ID
     * @param string $value
     **/
    public function SetPrepayid($value)
    {
        $this->values['prepayid'] = $value;
    }
    /**
     * 获取支付交易会话ID
     * @return 值
     **/
    public function GetPrepayid()
    {
        return $this->values['prepayid'];
    }

    /**
     * 设置扩展字段，暂时固定值Sign=WXPay
     * @param string $value
     **/
    public function SetPackage($value)
    {
        $this->values['package'] = $value;
    }
    /**
     * 获取扩展字段
     * @return 值
     **/
    public function GetPackage()
    {
        return $this->values['package'];
    }

    /**
     * 设置随机串
     * @param string $value
     **/
    public function SetNoncestr($value)
    {
        $this->values['noncestr'] = $value;
    }
    /**
     * 获取随机串
     * @return 值
     **/
    public function GetNoncestr()
    {
        return $this->values['noncestr'];
    }

    /**
     * 设置时间戳
     * @param string $value
     **/
    public function SetTimestamp($value)
    {
        $this->values['timestamp'] = $value;
    }
    /**
     * 获取时间戳
     * @return 值
     **/
    public function GetTimestamp()
    {
        return $this->values['timestamp'];
    }

   /**
     * 设置签名
     * @param string $value
     **/
    /* public function SetSign($value)
       {
           $this->values['sign'] = $value;
       }*/
    /**
     * 获取签名
     * @return 值
     **/
    public function GetSign()
    {
        return $this->values['sign'];
    }
} 