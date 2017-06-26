<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/16
 * Time: 18:30
 */

namespace frontend\business;
use common\components\SystemParamsUtil;
use common\models\FundInfo;

class FundUtil
{
    /**
     * 根据用户id获取基金信息
     * @param $user_id
     */
    public static function GetFundByUserId($user_id)
    {
        return FundInfo::findOne([
            'user_id'=>$user_id
        ]);
    }

    /**
     * 获取美愿基金新模型
     * @param $user
     */
    public static function GetFundNewModel($user)
    {
        $sourceMoney = SystemParamsUtil::GetSystemParam('system_fund_init_money',true);
        if(!isset($sourceMoney) || empty($sourceMoney))
        {
            $sourceMoney = '3000.00';
        }
        $rc = new FundInfo();
        $rc->user_id = $user->account_id;
        $rc->red_packets_money = 0;
        $rc->credit_money = 0;//$sourceMoney; 初始额度为0
        $rc->credit_balance = 0;//刚注册可用度改为0
        $rc->borrow_money_sum = 0;
        $rc->cashing_sum = 0;
        return $rc;
    }
}