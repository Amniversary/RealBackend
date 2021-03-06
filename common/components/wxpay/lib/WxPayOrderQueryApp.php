<?php
/**
* 2015-06-29 修复签名问题
**/
//require_once "WxPay.BeanstalkConfig.php";
//require_once "WxPay.Exception.php";
namespace common\components\wxpay\lib;

/**
 * 
 * 订单查询输入对象
 * @author widyhu
 *
 */
class WxPayOrderQueryApp extends WxPayAppDataBase
{
	/**
	* 设置微信分配的公众账号ID
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
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}


	/**
	* 设置微信的订单号，优先使用
	* @param string $value 
	**/
	public function SetTransaction_id($value)
	{
		$this->values['transaction_id'] = $value;
	}
	/**
	* 获取微信的订单号，优先使用的值
	* @return 值
	**/
	public function GetTransaction_id()
	{
		return $this->values['transaction_id'];
	}
	/**
	* 判断微信的订单号，优先使用是否存在
	* @return true 或 false
	**/
	public function IsTransaction_idSet()
	{
		return array_key_exists('transaction_id', $this->values);
	}


	/**
	* 设置商户系统内部的订单号，当没提供transaction_id时需要传这个。
	* @param string $value 
	**/
	public function SetOut_trade_no($value)
	{
		$this->values['out_trade_no'] = $value;
	}
	/**
	* 获取商户系统内部的订单号，当没提供transaction_id时需要传这个。的值
	* @return 值
	**/
	public function GetOut_trade_no()
	{
		return $this->values['out_trade_no'];
	}
	/**
	* 判断商户系统内部的订单号，当没提供transaction_id时需要传这个。是否存在
	* @return true 或 false
	**/
	public function IsOut_trade_noSet()
	{
		return array_key_exists('out_trade_no', $this->values);
	}


	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}

    /**
     *
     * 查询订单情况
     * @param string $out_trade_no  商户订单号
     * @param int $succCode         查询订单结果
     * @return 1 支付成功，3用户支付中，4失败
     */
    public static function CheckOrderAppResult($out_trade_no,&$outInfo,$isOther = false)
    {
        $queryOrderInput = new WxPayOrderQuery();
        $queryOrderInput->SetOut_trade_no($out_trade_no);
        if ($isOther) {
            $res = WxPayApi::orderQueryAppOther($queryOrderInput);
        } else {
            $res = WxPayApi::orderQueryApp($queryOrderInput);
        }

        if($res['return_code'] !== 'SUCCESS')
        {
            $status = 0;
            $outInfo['trade_state_desc'] = $res['return_msg'];   //状态描述

            return $status;
        }

        if($res['result_code'] !== 'SUCCESS')
        {
            $status = 0;
            $outInfo['trade_state_desc'] = $res['err_code_des']; //状态描述
            return $status;
        }

        switch($res['trade_state'])
        {
            case 'SUCCESS':
                $status = 1;    //支付成功
                break;
            case 'USERPAYING':
                $status = 3;   //用户支付中
                break;
            default:
                $status = 4;   //失败
        }
        $outInfo['openid'] = $res['openid'];   //用户标识
        $outInfo['trade_no'] = $res['transaction_id'];   //微信单号
        $outInfo['total_fee'] = $res['total_fee'];   //订单金额
        $outInfo['out_trade_no'] = $res['out_trade_no'];   //商户订单号
        $outInfo['attach'] = $res['attach'];   //附加数据
        $outInfo['trade_state_desc'] = $res['trade_state_desc'];   //状态描述
        return $status;
    }
}

