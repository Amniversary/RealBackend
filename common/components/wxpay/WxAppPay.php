<?php
namespace common\components\wxpay;
use common\components\wxpay\lib\WxPayApi;
use common\components\wxpay\lib\WxPayOtherPay;
use common\components\wxpay\lib\WxPayAppPay;
use common\components\wxpay\lib\WxPayException;
use common\components\wxpay\lib\WxPayConfig;
use common\components\wxpay\lib\WxPayJsApiPay;

//require_once "../lib/WxPay.Api.php";
/**
 * 
 * APP支付实现类
 * 该类实现了从微信公众平台获取code、通过code获取openid和access_token、
 * 生成jsapi支付js接口所需的参数、生成获取共享收货地址所需的参数
 * 
 * 该类是微信支付提供的样例程序，商户可根据自己的需求修改，或者使用lib中的api自行开发
 * 
 * @author widy
 *
 */
class WxAppPay
{
	
	/**
	 * 
	 * 获取jsapi支付的参数
	 * @param array $UnifiedOrderResult 统一支付接口返回的数据
	 * @throws WxPayException
	 * 
	 * @return json数据，可直接填入js函数作为参数
	 */
	public function GetAppPayParameters($UnifiedOrderResult,$isBackArray = false)
	{
		if(!array_key_exists("appid", $UnifiedOrderResult)
		|| !array_key_exists("prepay_id", $UnifiedOrderResult)
		|| $UnifiedOrderResult['prepay_id'] == "")
		{
			throw new WxPayException("参数错误");
		}
		$appPay = new WxPayAppPay();
        $appPay->SetAppid($UnifiedOrderResult["appid"]);
        $appPay->SetPartnerid(WxPayConfig::MCHID);
		$timeStamp = time();
        $appPay->SetTimeStamp("$timeStamp");
        $appPay->SetNonceStr(WxPayApi::getNonceStr());
        $appPay->SetPackage('Sign=WXPay');
        $appPay->SetPrepayid($UnifiedOrderResult['prepay_id']);
        $appPay->SetSign($appPay->MakeSign());
        $parameters = $appPay->GetValues();
        if(!$isBackArray)
        {
            $parameters = json_encode($appPay->GetValues());
        }
		return $parameters;
	}

    /**
     *
     * 获取jsapi支付的参数
     * @param array $UnifiedOrderResult 统一支付接口返回的数据
     * @throws WxPayException
     *
     * @return json数据，可直接填入js函数作为参数
     */
    public function GetOtherPayParameters($UnifiedOrderResult,$isBackArray = false)
    {
        if(!array_key_exists("appid", $UnifiedOrderResult)
            || !array_key_exists("prepay_id", $UnifiedOrderResult)
            || $UnifiedOrderResult['prepay_id'] == "")
        {
            throw new WxPayException("参数错误");
        }
        $appPay = new WxPayOtherPay();
        $appPay->SetAppid($UnifiedOrderResult["appid"]);
        $appPay->SetPartnerid(WxPayConfig::getConfig('MCHID'));
        $timeStamp = time();
        $appPay->SetTimeStamp("$timeStamp");
        $appPay->SetNonceStr(WxPayApi::getNonceStr());
        $appPay->SetPackage('Sign=WXPay');
        $appPay->SetPrepayid($UnifiedOrderResult['prepay_id']);
        $appPay->SetOtherSign();
        $parameters = $appPay->GetValues();
        if(!$isBackArray)
        {
            $parameters = json_encode($appPay->GetValues());
        }
		return $parameters;
	}

	
	/**
	 * 
	 * 拼接签名字符串
	 * @param array $urlObj
	 * 
	 * @return 返回已经拼接好的字符串
	 */
	private function ToUrlParams($urlObj)
	{
		$buff = "";
		foreach ($urlObj as $k => $v)
		{
			if($k != "sign"){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}
	
	/**
	 * 
	 * 获取地址js参数
	 * 
	 * @return 获取共享收货地址js函数需要的参数，json格式可以直接做参数使用
	 */
	public function GetEditAddressParameters()
	{	
		$getData = $this->data;
		$data = array();
		$data["appid"] = WxPayConfig::APPID;
		$data["url"] = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$time = time();
		$data["timestamp"] = "$time";
		$data["noncestr"] = "1234568";
		$data["accesstoken"] = $getData["access_token"];
		ksort($data);
		$params = $this->ToUrlParams($data);
		$addrSign = sha1($params);
		
		$afterData = array(
			"addrSign" => $addrSign,
			"signType" => "sha1",
			"scope" => "jsapi_address",
			"appId" => WxPayConfig::APPID,
			"timeStamp" => $data["timestamp"],
			"nonceStr" => $data["noncestr"]
		);
		$parameters = json_encode($afterData);
		return $parameters;
	}

}