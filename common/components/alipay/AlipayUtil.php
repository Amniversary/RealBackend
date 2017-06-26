<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/30
 * Time: 10:07
 */

namespace common\components\alipay;
use common\components\alipay\lib\AlipayNotify;
use common\components\alipay\lib\AlipaySubmit;
use frontend\business\OtherPayUtil;
use yii\log\Logger;

class AlipayUtil
{
    /**
     * 处理支付宝支付通知
     * @return string
     */
    public static function DealNotify()
    {
        $alipay_config = require("alipay.config.php");

        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        //\Yii::getLogger()->log('支付宝验证通知:'.$_POST['trade_status'],Logger::LEVEL_ERROR);
        if($verify_result)
        {
            //验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代


            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //商户订单号

            $out_trade_no = $_POST['out_trade_no'];

            //支付宝交易号

            $trade_no = $_POST['trade_no'];

            //交易状态
            $trade_status = $_POST['trade_status'];

            $rst = '1';// 1 失败  2 成功

            if($_POST['trade_status'] == 'TRADE_FINISHED')
            {
                //
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
                $rst = '2';
            }
            else if ($_POST['trade_status'] == 'TRADE_SUCCESS')
            {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");

                $rst = '2';
            }

            $params = [
                'trade_no'=>isset($_POST['trade_no'])?$_POST['trade_no']:'',
                'trade_ok'=>$rst,
                'out_trade_no'=>isset($_POST['out_trade_no'])?$_POST['out_trade_no']:'',
                'body'=>isset($_POST['body'])?$_POST['body']:'',
                'total_fee'=>isset($_POST['total_fee'])?$_POST['total_fee']:'',
            ];
            //\Yii::getLogger()->log('支付宝验证通知body:'.$_POST['body'],Logger::LEVEL_ERROR);
            $pay_type = '3';

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
                        \Yii::getLogger()->log('支付通知时body参数解析异常，出现多个等号，原来参数：'.$parItems[$i],Logger::LEVEL_ERROR);
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
                    \Yii::getLogger()->log('进入支付通知时结果处理 params：'.var_export($params,true),Logger::LEVEL_ERROR);

                    if(!OtherPayUtil::DealOtherPayResult($params,$pay_type,$pay_target,$error))
                    {
                        \Yii::getLogger()->log('支付通知时结果处理异常：'.$error,Logger::LEVEL_ERROR);
                    }
                }
            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            return  "success";		//请不要修改或删除

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else
        {
            \Yii::getLogger()->log('进入支付宝支付通知，验证失败',Logger::LEVEL_ERROR);
            //验证失败
            return "fail";

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }

    /**
     * 获取支付宝支付配置
     */
    public static function GetAlipayConfig()
    {
        return require("alipay.config.php");
    }

    /**
     * 支付宝查询订单状态
     * @return 0 交易失败  2 未知异常（可能网络，不做处理） 3 签名验证错误 4 交易失败
     */
    public static function QueryOrderStatus($out_trade_no,$trade_no,&$outTradeInfo)
    {
        $alipay_config = self::GetAlipayConfig();
        $alipay_config['sign_type']='MD5';
//构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "single_trade_query",
            "partner" => trim($alipay_config['partner']),
            "trade_no"	=> $trade_no,
            "out_trade_no"	=> $out_trade_no,
            "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
        );

//建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestHttp($parameter);
//解析XML
//注意：该功能PHP5环境及以上支持，需开通curl、SSL等PHP配置环境。建议本地调试时使用PHP开发软件

        $data = json_decode(json_encode((array) simplexml_load_string($html_text)), true);
        //var_dump($data);
        //\Yii::getLogger()->log('data:'.var_export($data,true),Logger::LEVEL_ERROR);
        if(!isset($data) || empty($data))
        {
            \Yii::getLogger()->log('获取账单状态未知错误：'.$html_text,Logger::LEVEL_ERROR);
            return 2;//未知错误
        }
        if($data['is_success'] !== 'T')
        {
            if($data['error'] === 'TRADE_NOT_EXIST')
            {
                return 0;//记录不存在，一定是失败
            }
            else
            {
                return 2;//未知错误
            }
            \Yii::getLogger()->log('获取账单状态未知错误1：'.$html_text,Logger::LEVEL_ERROR);
        }
        if(!isset($data['response']) ||
            !isset($data['response']['trade']) ||
            !isset($data['sign']) ||
            !isset($data['sign_type'])
        )
        {
            \Yii::getLogger()->log('获取账单状态未知错误2：'.$html_text,Logger::LEVEL_ERROR);
            return 2;//未知错误
        }
        $trade = $data['response']['trade'];
        if(!is_array($trade) || count($trade) <= 0)
        {
            \Yii::getLogger()->log('获取账单状态未知错误3：'.$html_text,Logger::LEVEL_ERROR);
            return 2;//未知错误
        }
        $sign = $data['sign'];
        $signType =  $data['sign_type'];
        $alipay_config['sign_type']=$signType;
        $alipayNofiy = new AlipayNotify($alipay_config);
        if($alipayNofiy->getSignVeryfy($trade,$sign))
        {
            $trade_status = $trade['trade_status'];
            if(!in_array($trade_status,['TRADE_FINISHED','TRADE_SUCCESS']))
            {
                return 4;//交易失败
            }

            $outTradeInfo = $trade;
            //\Yii::getLogger()->log('outTrade:'.var_export($trade,true),Logger::LEVEL_ERROR);
        }
        else
        {
            \Yii::getLogger()->log('账单签名错误：'.$html_text,Logger::LEVEL_ERROR);
            return 3;//签名错误
        }

        return 1;
        //有账单的数据示例：
        /*array(5) {
        ["is_success"]=> string(1) "T"
        ["request"]=> array(1) {
            ["param"]=> array(4) {
                [0]=> string(5) "utf-8"
                [1]=> string(18) "single_trade_query"
                [2]=> string(16) "2088121659089991"
                [3]=> string(19) "ZHF-RG-15-12-301167" }
        }
        ["response"]=> array(1) {
            ["trade"]=> array(25) {
                ["body"]=> string(34) "pay_target=recharge&charge_id=1167"
                ["buyer_email"]=> string(11) "15552832298"
                ["buyer_id"]=> string(16) "2088712082706409"
                ["discount"]=> string(4) "0.00"
                ["flag_trade_locked"]=> string(1) "0"
                ["gmt_create"]=> string(19) "2016-03-18 21:08:46"
                ["gmt_last_modified_time"]=> string(19) "2016-03-18 21:08:46"
                ["gmt_payment"]=> string(19) "2016-03-18 21:08:46"
                ["is_total_fee_adjust"]=> string(1) "F"
                ["operator_role"]=> string(1) "B"
                ["out_trade_no"]=> string(19) "ZHF-RG-15-12-301167"
                ["payment_type"]=> string(1) "1"
                ["price"]=> string(5) "10.00"
                ["quantity"]=> string(1) "1"
                ["seller_email"]=> string(17) "2181325630@qq.com"
                ["seller_id"]=> string(16) "2088121659089991"
                ["subject"]=> string(12) "充值付款"
                ["time_out"]=> string(19) "2016-06-17 21:08:46"
                ["time_out_type"]=> string(14) "finishFPAction"
                ["to_buyer_fee"]=> string(4) "0.00"
                ["to_seller_fee"]=> string(5) "10.00"
                ["total_fee"]=> string(5) "10.00"
                ["trade_no"]=> string(28) "2016031821001004400210531006"
                ["trade_status"]=> string(13) "TRADE_SUCCESS"
                ["use_coupon"]=> string(1) "F" }
        }
        ["sign"]=> string(32) "255fede3c2a92fd6bab05731dcf80c76"
        ["sign_type"]=> string(3) "MD5"
        }*/
//请在这里加上商户的业务逻辑程序代码

//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

//获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
//解析XML


    }
}