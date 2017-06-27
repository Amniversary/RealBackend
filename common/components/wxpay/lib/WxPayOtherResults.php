<?php
//require_once "WxPay.Config.php";
//require_once "WxPay.Exception.php";
namespace common\components\wxpay\lib;
use yii\log\Logger;

/**
 * 
 * 接口调用结果类
 * @author widyhu
 *
 */
class WxPayOtherResults extends WxPayDataBase
{
	/**
	 * 
	 * 检测签名
	 */
	public function CheckSign()
	{
		//fix异常
		if(!$this->IsSignSet()){
            \Yii::getLogger()->log('no sign data:'.var_export($this->values,true),Logger::LEVEL_ERROR);
			throw new WxPayException("签名错误！");
		}

		$sign = $this->MakeOtherSign();
		if($this->GetSign() == $sign){
			return true;
		}
        \Yii::getLogger()->log('wxpay nowsign:'.$sign.' send sign:'.$this->GetSign(), Logger::LEVEL_ERROR);
		throw new WxPayException("签名错误！");
	}
	
	/**
	 * 
	 * 使用数组初始化
	 * @param array $array
	 */
	public function FromArray($array)
	{
		$this->values = $array;
	}
	
	/**
	 * 
	 * 使用数组初始化对象
	 * @param array $array
	 * @param 是否检测签名 $noCheckSign
	 */
	public static function InitFromArray($array, $noCheckSign = false)
	{
		$obj = new self();
		$obj->FromArray($array);
		if($noCheckSign == false){
			$obj->CheckSign();
		}
        return $obj;
	}
	
	/**
	 * 
	 * 设置参数
	 * @param string $key
	 * @param string $value
	 */
	public function SetData($key, $value)
	{
		$this->values[$key] = $value;
	}
	
    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
	public static function Init($xml)
	{	
		$obj = new self();
		$obj->FromXml($xml);
		//fix bug 2015-06-29
		if($obj->values['return_code'] != 'SUCCESS'){
			 return $obj->GetValues();
		}
		$obj->CheckSign();
        return $obj->GetValues();
	}
}