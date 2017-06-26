<?php
namespace common\components\wxpay;
//ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);

/*require_once "../lib/WxPay.Api.php";
require_once '../lib/WxPay.Notify.php';
require_once 'log.php';*/

use common\components\wxpay\lib\WxPayAppNotify;
use frontend\business\OtherPayUtil;
use yii\log\Logger;

//初始化日志
/*$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);*/

class AppPayNotifyCallBack extends WxPayAppNotify
{
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
        //{"appid":"wx3cf21f506b7cd9ad","attach":"test=test&type=app","bank_type":"CFT","cash_fee":"1","fee_type":"CNY","is_subscribe":"N","mch_id":"1302829601","nonce_str":"0sjgmkvsfckaf198m9a32tds3ee0x9zi","openid":"orIt2uOwy_8K9vlHOqQJhkxAZHf0","out_trade_no":"130283730120160223160409","result_code":"SUCCESS","return_code":"SUCCESS","sign":"AD5B2496D496D752322C99CC90F9CE87","time_end":"20160223160415","total_fee":"1","trade_type":"APP","transaction_id":"1010210363201602233477896074"}
        $rst = ($data['return_code'] === 'SUCCESS' && $data['result_code'] === 'SUCCESS');
        if(!$rst)
        {
            $msg = '业务处理异常';
            \Yii::getLogger()->log("wxpay app back error:" . json_encode($data), Logger::LEVEL_ERROR);
            return false;
        }
        $params = [
            'trade_no'=>isset($data['transaction_id'])?$data['transaction_id']:'',
            'trade_ok'=>'2',
            'out_trade_no'=>isset($data['out_trade_no'])?$data['out_trade_no']:'',
            'body'=>isset($data['attach'])?$data['attach']:'',
            'total_fee'=>isset($data['total_fee'])?$data['total_fee']:'',
        ];
        $pay_type = '4';
        if(empty($params['body']))
        {
            \Yii::getLogger()->log('wxpay支付通知时body参数为空',Logger::LEVEL_ERROR);
        }
        else
        {
            $body = $params['body'];
            $parItems = explode('&',$body);
            $len = count($parItems);
            for($i = 0; $i < $len; $i++ )
            {
                $items = explode('=',$parItems[$i]);
                if(count($items) === 2)
                {
                    $params[$items[0]] = $items[1];
                }
                else
                {
                    \Yii::getLogger()->log('wxpay支付通知时body参数解析异常，出现多个等号，原来参数：'.$parItems[$i],Logger::LEVEL_ERROR);
                }
            }
            unset($params['body']);
            if(!isset($params['pay_target']) || empty($params['pay_target']))
            {
                \Yii::getLogger()->log('wxpay支付通知时body参数解析异常，pay_target参数为空',Logger::LEVEL_ERROR);
            }
            else
            {
                $pay_target = $params['pay_target'];
                unset($params['pay_target']);
                $error = '';
                //\Yii::getLogger()->log('进入支付通知时结果处理 params：'.var_export($params,true),Logger::LEVEL_ERROR);
                if(!OtherPayUtil::DealOtherPayResult($params,$pay_type,$pay_target,$error))
                {
                    \Yii::getLogger()->log('wxpay支付通知时结果处理异常：'.$error,Logger::LEVEL_ERROR);
                    $msg = $error;
                    return false;
                }
            }
        }
		return true;
	}
}

/*Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);*/
