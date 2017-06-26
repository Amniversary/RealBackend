<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/2/17
 * Time: 17:09
 */

namespace common\components;


use common\components\wxpay\lib\WxPayApi;
use common\components\wxpay\lib\WxPayConfig;
use common\components\wxpay\lib\WxPayUnifiedOrder;
use common\components\wxpay\WxAppPay;
use yii\base\Exception;
use yii\log\Logger;

class WeiXinUtil
{
    /**
     * 获取微信支付app支付参数
     * @param $input
     * @param $outParams
     * @param $error
     * @return bool
     */
    public static function GetAppPayParams($inputParams,&$outParams,&$error)
    {   
        if(!is_array($inputParams))
        {
            $error = '参数必须是数组';
            return false;
        }
        try
        {
            $tools = new WxAppPay();
//②、统一下单
            //\Yii::getLogger()->log('pay_money:'.$out['real_pay_money'], Logger::LEVEL_ERROR);
            $input = new WxPayUnifiedOrder();
            $input->SetBody($inputParams['dis']);
            $input->SetAttach($inputParams['body']);
            $input->SetOut_trade_no($inputParams['out_trade_no']);
            $input->SetTotal_fee(doubleval($inputParams['real_pay_money'])*100);
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetNotify_url('http://'.$_SERVER['HTTP_HOST'].WxPayConfig::NOTIFY_URL_APP);
            $input->SetTrade_type("APP");
            //var_dump($input);exit;
            //\Yii::getLogger()->log('wxpaytttttttttttttttt:before', Logger::LEVEL_ERROR);
            $order = WxPayApi::unifiedOrderForApp($input);
            //\Yii::getLogger()->log('pay_money:'.var_export($order,true), Logger::LEVEL_ERROR);
            //$this->printf_info($order);
            //\Yii::getLogger()->log('wxpaytttttttttttttttt:'.var_export($order,true), Logger::LEVEL_ERROR);
            $outParams = $tools->GetAppPayParameters($order,true);
            $outParams['out_trade_no'] = $inputParams['out_trade_no'];
            //\Yii::getLogger()->log('out params:'.var_export($outParams,true), Logger::LEVEL_ERROR);
        }
        catch(Exception $e)
        {             
            $error = $e->getMessage();
            \Yii::getLogger()->log('微信支付时发生了错误，在类WeiXinUtil的GetAppPayParams方法中:'.var_export($error,true), Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }


    /**
     * 获取微信支付a支付参数
     * @param $input
     * @param $outParams
     * @param $error
     * @return bool
     */
    public static function GetOtherPayParams($inputParams,&$outParams,&$error)
    {
        if(!is_array($inputParams))
        {
            $error = '参数必须是数组';
            return false;
        }
        try
        {
            $tools = new WxAppPay();
//②、统一下单
            //\Yii::getLogger()->log('pay_money:'.$out['real_pay_money'], Logger::LEVEL_ERROR);
            $input = new WxPayUnifiedOrder();
            $input->SetBody($inputParams['dis']);
            $input->SetAttach($inputParams['body']);
            $input->SetOut_trade_no($inputParams['out_trade_no']);
            $input->SetTotal_fee(doubleval($inputParams['real_pay_money'])*100);
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetNotify_url('http://'.$_SERVER['HTTP_HOST'].WxPayConfig::NOTIFY_URL_APP);
            $input->SetTrade_type("APP");
            //var_dump($input);exit;
            //\Yii::getLogger()->log('wxpaytttttttttttttttt:before', Logger::LEVEL_ERROR);
            $order = WxPayApi::unifiedOrderForOther($input);
            //\Yii::getLogger()->log('pay_money:'.var_export($order,true), Logger::LEVEL_ERROR);
            //$this->printf_info($order);
            //\Yii::getLogger()->log('wxpaytttttttttttttttt:'.var_export($order,true), Logger::LEVEL_ERROR);
            $outParams = $tools->GetOtherPayParameters($order,true);
            $outParams['out_trade_no'] = $inputParams['out_trade_no'];
            //\Yii::getLogger()->log('out params:'.var_export($outParams,true), Logger::LEVEL_ERROR);
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            return false;
        }
        return true;
    }


    /**
     * 获取微信分享签名
     * @param $url
     * @param $noncestr
     * @param $timestamp
     * @return string
     */
    public static function GetShareSign($url,$noncestr='meiyuanduo2jd2oDGFERETRE',$timestamp='1455695941')
    {
        $jsapi_ticket = \Yii::$app->wechat->getJsApiTicket();
        //\Yii::getLogger()->log('$jsapi_ticket==='.$jsapi_ticket,Logger::LEVEL_ERROR);
        $ary = [
            'jsapi_ticket'=>$jsapi_ticket,
            'noncestr'=>$noncestr,
            'timestamp'=>$timestamp,
            'url'=>$url,
        ];
        //\Yii::getLogger()->log('wxary=:'.var_export($ary,true).'     appid=='.\Yii::$app->wechat->appId,Logger::LEVEL_ERROR);
        ksort($ary);
        $singStr= '';
        foreach($ary as $key=>$value)
        {
            $singStr .= $key.'='.$value.'&';
        }
        if(!empty($singStr))
        {
            $singStr = substr($singStr,0,strlen($singStr)-1);
        }
        //var_dump(Html::encode($singStr));
        return sha1($singStr);
    }
} 