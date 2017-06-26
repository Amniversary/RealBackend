<?php
namespace common\components\llpay;

use common\components\llpay\lib\LLpayNotify;
use frontend\business\OtherPayUtil;
use yii\log\Logger;

class LlpayNotifyUtil
{

    public static function GetLLpayConfig()
    {
        return require(__DIR__.'/llpay.config.php');
    }

    /**
     * 处理支付结果 结果示例
    array (
    'oid_partner' => '201601071000671903',
    'sign_type' => 'MD5',
    'dt_order' => '20160315140701',
    'no_order' => 'LLP1602018344',
    'oid_paybill' => '2016031538848398',
    'money_order' => '0.01',
    'result_pay' => 'SUCCESS',
    'settle_date' => '20160315',
    'info_order' => 'pay_target=checkbankcard',
    'pay_type' => 'D',
    'bank_code' => '01030000',
    'no_agree' => '',
    'id_type' => '',
    'id_no' => '',
    'acct_name' => '',
    )
     */
    public static function DealNotify()
    {
        $rst = ['ret_code'=>'9999','ret_msg'=>'验签失败'];
        $llpay_config = require(__DIR__.'/llpay.config.php');
        //计算得出通知验证结果
        $llpayNotify = new LLpayNotify($llpay_config);
        $llpayNotify->verifyNotify();
        if ($llpayNotify->result)
        { //验证成功
            //获取连连支付的通知返回参数，可参考技术文档中服务器异步通知参数列表
            $no_order = $llpayNotify->notifyResp['no_order'];//商户订单号
            $oid_paybill = $llpayNotify->notifyResp['oid_paybill'];//连连支付单号
            $result_pay = $llpayNotify->notifyResp['result_pay'];//支付结果，SUCCESS：为支付成功
            $money_order = $llpayNotify->notifyResp['money_order'];// 支付金额
            $back_info = $llpayNotify->notifyResp['info_order'];
            if($result_pay == "SUCCESS")
            {
                //\Yii::getLogger()->log('连连支付成功',Logger::LEVEL_ERROR);
                //请在这里加上商户的业务逻辑程序代(更新订单状态、入账业务)
                //——请根据您的业务逻辑来编写程序——
                $params = [
                    'trade_no'=>isset($oid_paybill)?$oid_paybill:'',
                    'trade_ok'=>'2',
                    'out_trade_no'=>isset($no_order)?$no_order:'',
                    'body'=>isset($back_info)?$back_info:'',
                    'total_fee'=>isset($money_order)?$money_order:'',
                ];
                $pay_type = '5';
                if(empty($params['body']))
                {
                    \Yii::getLogger()->log('支付通知时body参数为空',Logger::LEVEL_ERROR);
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
                            \Yii::getLogger()->log('llapy支付通知时body参数解析异常，出现多个等号，原来参数：'.$parItems[$i],Logger::LEVEL_ERROR);
                        }
                    }
                    unset($params['body']);
                    if(!isset($params['pay_target']) || empty($params['pay_target']))
                    {
                        \Yii::getLogger()->log('支付通知时body参数解析异常，pay_target参数为空',Logger::LEVEL_ERROR);
                    }
                    else
                    {
                        $pay_target = $params['pay_target'];
                        unset($params['pay_target']);
                        $error = '';
                        //\Yii::getLogger()->log('进入支付通知时结果处理 params：'.var_export($params,true),Logger::LEVEL_ERROR);
                        if(!OtherPayUtil::DealOtherPayResult($params,$pay_type,$pay_target,$error))
                        {
                            \Yii::getLogger()->log('支付通知时结果处理异常：'.$error,Logger::LEVEL_ERROR);
                        }
                    }
                }
            }
            else
            {
                \Yii::getLogger()->log('连连支付打赏支付失败：'.file_get_contents("php://input"), Logger::LEVEL_ERROR);
            }
            //file_put_contents("log.txt", "异步通知 验证成功\n", FILE_APPEND);
            $rst['ret_code']='0000';
            $rst['ret_msg']='交易成功';
            //die("{'ret_code':'0000','ret_msg':'交易成功'}"); //请不要修改或删除
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else
        {
            \Yii::getLogger()->log('llpay签名验证失败',Logger::LEVEL_ERROR);
            //file_put_contents("log.txt", "异步通知 验证失败\n", FILE_APPEND);
            //验证失败
            //die("{'ret_code':'9999','ret_msg':'验签失败'}");
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
        return json_encode($rst);
    }
}
?>