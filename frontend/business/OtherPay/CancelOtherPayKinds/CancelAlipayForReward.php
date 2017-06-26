<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/4
 * Time: 13:12
 */

namespace frontend\business\OtherPay\CancelOtherPayKinds;


use frontend\business\OtherPay\ICancelOtherPay;
use frontend\business\RewardUtil;
use yii\log\Logger;

class CancelAlipayForReward implements ICancelOtherPay
{
    public function CancelPay($params,&$error)
    {
        if(!isset($params))
        {
            $error = '参数不能为空';
            return;
        }
        if(!isset($params['bill_no']) || empty($params['bill_no']))
        {
            $error ='账单号不能为空';
            return false;
        }
        $billNo = $params['bill_no'];
        $rewardInfo = RewardUtil::GetRewardInfoByBillNo($billNo);
        if(!isset($rewardInfo))
        {
            $error = '打赏记录找不到，取消失败';
            \Yii::getLogger()->log($error.' bill_no:'.$billNo, Logger::LEVEL_ERROR);
            return false;
        }
        if(!RewardUtil::CancelRewardByOtherPay($rewardInfo,$error))
        {
            return false;
        }
        return true;
    }
} 