<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/30
 * Time: 16:04
 */

namespace frontend\business;


use common\components\PhpLock;
use common\components\SystemParamsUtil;
use yii\log\Logger;

class OtherPayUtil
{
    /**
     * 获取第三方支付参数
     * @param $params
     * @param $pay_type
     * @param $pay_target   充值 recharge
     * @param $outParams 返回支付参数
     * @param $error
     */
    public static function GetOtherPayParams($params,$pay_type,$pay_target,&$outParams,&$error)
    {
        $configFile = __DIR__.'/OtherPay/GetPayParamsConfig.php';
        if(!file_exists($configFile))//检查文件目录是否存在 true  false
        {
            $error = '系统错误，找不到配置文件';
            \Yii::getLogger()->log($error.' '.$configFile,Logger::LEVEL_ERROR);
            return false;
        }
        $configInfo = require($configFile);
        if(!isset($configInfo[$pay_type]))
        {
            $error = '未实现的支付方式，找不到处理类';
            \Yii::getLogger()->log($error.' '.$pay_type, Logger::LEVEL_ERROR);
            return false;
        }
        if(!isset($configInfo[$pay_type][$pay_target]))
        {
            $error = '未实现的支付方式，找不到处理类1';
            \Yii::getLogger()->log($error.' '.$pay_type.' '.$pay_target, Logger::LEVEL_ERROR);
            return false;
        }
        $dealClass = $configInfo[$pay_type][$pay_target];
//        \Yii::getLogger()->log('对应处理类:'.$dealClass,Logger::LEVEL_ERROR);
        if(!class_exists($dealClass))
        {
            $error = '对应处理类不存在';
            \Yii::getLogger()->log($error. ' '.$dealClass, Logger::LEVEL_ERROR);
            return false;
        }
        if(isset($params['params']) && is_array($params['params']))
        {
            $paramsInner = $params['params'];
            foreach($paramsInner as  $key => $value)
            {
                $params[$key] = $value;
            }
            unset($params['params']);
        }
        $instance = new $dealClass;
        if(!$instance->GetPayParams($params,$outParams,$error))
        {
            return false;
        }
        return true;
    }


    /**
     * 处理第三方支付结果
     * 充值需要的参数：
     * @param  $params
     *[
        'trade_no'=>'',  //第三方交易账单号
        'trade_ok'=>'2', //交易状态 2 正常
        'out_trade_no'=>'',//交易站单号
        'total_fee'=>'', //交易金额
        'charge_id'=>'', //交易记录号
        'device_type'=>''//登录类型
     * ]
     * @param $pay_type  3 阿里支付  4 微信支付   6 苹果支付 100 web微信支付
     * @param $pay_target
     * @param $error
     * @return bool
     */
    public static function DealOtherPayResult($params,$pay_type,$pay_target,&$error)
    {
        $configFile = __DIR__.'/OtherPay/OtherPayResultConfig.php';
        if(!file_exists($configFile))
        {
            $error = '系统错误，找不到配置文件';
            \Yii::getLogger()->log($error.' '.$configFile,Logger::LEVEL_ERROR);
            return false;
        }
        $configInfo = require($configFile);
        if(!isset($configInfo[$pay_type]))
        {
            $error = '未实现的支付方式，找不到处理类';
            \Yii::getLogger()->log($error.' '.$pay_type, Logger::LEVEL_ERROR);
            return false;
        }
        if(!isset($configInfo[$pay_type][$pay_target]))
        {
            $error = '未实现的支付方式，找不到处理类1';
            \Yii::getLogger()->log($error.' '.$pay_type.' '.$pay_target, Logger::LEVEL_ERROR);
            return false;
        }
        $dealClass = $configInfo[$pay_type][$pay_target];
        if(!class_exists($dealClass))
        {
            $error = '对应处理类不存在';
            \Yii::getLogger()->log($error. ' '.$dealClass, Logger::LEVEL_ERROR);
            return false;
        }
        $instance = new $dealClass;
        if(!$instance->DoOtherPayResult($params, $error))
        {
            return false;
        }
        return true;
    }

    /**
     *取消第三方支付支持
     * @param $params
     * @param $pay_type
     * @param $pay_target
     * @param $error
     */
    public static function CancelRewardByOtherPay($params,$pay_type,$pay_target,&$error)
    {
        $configFile = __DIR__.'/OtherPay/CancelOtherPayConfig.php';
        if(!file_exists($configFile))
        {
            $error = '系统错误，找不到配置文件';
            \Yii::getLogger()->log($error.' '.$configFile,Logger::LEVEL_ERROR);
            return false;
        }
        $configInfo = require($configFile);
        if(!isset($configInfo[$pay_type]))
        {
            $error = '未实现的支付方式，找不到处理类';
            \Yii::getLogger()->log($error.' '.$pay_type, Logger::LEVEL_ERROR);
            return false;
        }
        if(!isset($configInfo[$pay_type][$pay_target]))
        {
            $error = '未实现的支付方式，找不到处理类1';
            \Yii::getLogger()->log($error.' '.$pay_type.' '.$pay_target, Logger::LEVEL_ERROR);
            return false;
        }
        $dealClass = $configInfo[$pay_type][$pay_target];
        if(!class_exists($dealClass))
        {
            $error = '对应处理类不存在';
            \Yii::getLogger()->log($error. ' '.$dealClass, Logger::LEVEL_ERROR);
            return false;
        }
        $instance = new $dealClass;
        if(!$instance->CancelPay($params, $error))
        {
            return false;
        }
        return true;
    }

    /**
     * 内购是否超额
     * @param $user_id
     * @param $payMoney
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function IsOverPay($user_id,$payMoney)
    {
        $key = sprintf('apple_pay_%s_%s',$user_id,date('Y-m-d'));
        $cacheData = \Yii::$app->cache->get($key);
        $readyPay = 0.0;
        if($cacheData === false)
        {
            $payLimit = SystemParamsUtil::GetSystemParam('apple_pay_over_money');
            $payLimit = doubleval($payLimit);
            if($payLimit <= 0)
            {
                $payLimit = 200.0;
            }
        }
        else
        {
            $payInfo = json_decode($cacheData,true);
            $payLimit = doubleval($payInfo['pay_limit']);
            $readyPay = doubleval($payInfo['ready_pay']);
        }
        $is_over = '0';
        if(($readyPay + $payMoney) > $payLimit)
        {
            $is_over = '1';
        }
        $rst = [
            'is_over'=>$is_over,
            'left_money'=>$readyPay
        ];
        return $rst;
    }
}