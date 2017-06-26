<?php
/**
 * Created by PhpStorm.
 * User=> John
 * Date=> 2015/12/12
 * Time=> 16=>15
 */

namespace frontend\controllers\MybackActions;


use common\components\alipay\AlipayUtil;
use common\components\PhpLock;
use frontend\business\ApiLogUtil;
use frontend\business\PersonalUserUtil;
use frontend\business\RechargeListUtil;
use frontend\business\ReportUtil;
use frontend\business\RewardUtil;
use frontend\business\UserAccountInfoUtil;
use frontend\business\WishUtil;
use yii\base\Action;
use yii\log\Logger;

class DealUnkownPayResultAction extends Action
{
    /**
     * 检查参数
     * @param $error
     * @return bool
     */
    private function check_post_params(&$error)
    {
        $error = '';
        //$rand_str, $time, $token,$data,$token_other
        if(!isset($_GET['backvelidatekey']) || empty($_GET['backvelidatekey']))
        {
            $error = '参数缺少';
            \Yii::getLogger()->log('lost param:'.var_export($_GET,true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    public function run()
    {
        //http://front.matewish.cn/myback/dealotherpay?backvelidatekey=ewjosjfe7200u0ujsjowjojfshcosheroewcsd46496sd8
        set_time_limit(0);
        $time1 = microtime(true);
        $error = '';
        if(!$this->check_post_params($error))
        {
            echo $error;
            exit;
        }
        $key = $_GET['backvelidatekey'];
        if($key !== \Yii::$app->params['backvelidatekey'])
        {
            \Yii::getLogger()->log('验证key不正确',Logger::LEVEL_ERROR);
            return;
        }

        $phpLock = new PhpLock('deal_unkown_other_payresult');
        $phpLock->lock();
        //处理充值
        $errorRechargeList =RechargeListUtil::GetUnkownOtherPayRechargeRecords(100);
        $len = count($errorRechargeList);
        foreach($errorRechargeList as $recharge)
        {
            $out_trade_no = $recharge->pay_bill;
            if(empty($out_trade_no))
            {
                $recharge->status_result = 0;
                if(!$recharge->save())
                {
                    echo '保存充值记录错误';
                    \Yii::getLogger()->log(var_export($recharge->getErrors(),true),Logger::LEVEL_ERROR);
                    exit;
                }
            }
            $trade = null;
            $rst = AlipayUtil::QueryOrderStatus($out_trade_no,'',$trade);
            if($rst === 2)
            {
                continue;
            }
            if($rst === 1)
            {
                if(!RechargeListUtil::DealUnkownAliPayPayResult($trade,$error))
                {
                    echo '处理支付宝充值异常';
                    \Yii::getLogger()->log('处理支付宝充值异常：'.$error,Logger::LEVEL_ERROR);
                    exit;
                }
            }
            else
            {
                $recharge->status_result = 0;
                if(!$recharge->save())
                {
                    echo '保存充值记录错误';
                    \Yii::getLogger()->log(var_export($recharge->getErrors(),true),Logger::LEVEL_ERROR);
                    exit;
                }
            }
        }

        $rewardList = RewardUtil::GetAlipayUnkownPayResultRecords();
        $len += count($rewardList);
        foreach($rewardList as $reward)
        {
            $out_trade_no = $reward->pay_bill;
            if(empty($out_trade_no))
            {
                $reward->pay_status = 0;
                if(!$reward->save())
                {
                    echo '保存充值记录错误';
                    \Yii::getLogger()->log(var_export($reward->getErrors(),true),Logger::LEVEL_ERROR);
                    exit;
                }
            }
            $trade = null;
            $rst = AlipayUtil::QueryOrderStatus($out_trade_no,'',$trade);
            if($rst === 2)
            {
                continue;
            }
            if($rst === 1)
            {
                if(!RechargeListUtil::DealUnkownAliPayPayResult($trade,$error))
                {
                    echo '处理支付宝充值异常';
                    \Yii::getLogger()->log('处理支付宝充值异常：'.$error,Logger::LEVEL_ERROR);
                    exit;
                }
            }
            else
            {
                $reward->pay_status = 0;
                if(!$reward->save())
                {
                    echo '保存充值记录错误';
                    \Yii::getLogger()->log(var_export($reward->getErrors(),true),Logger::LEVEL_ERROR);
                    exit;
                }
            }
        }

        $time2 = microtime(true);
        $disTime = round($time2 - $time1, 3);//单位秒
        $apiLog = ApiLogUtil::GetNewModel('dealotherpay',strval($disTime),'deal_record_count:'.strval($len),'',$this->className());
        ApiLogUtil::SaveApiLog($apiLog);
        $phpLock->unlock();
        //输出处理结果
        echo 'ok record count:'.strval($len).' time:'.date('Y-m-d H:i:s');
    }
} 