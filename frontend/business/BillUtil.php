<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/23
 * Time: 11:13
 */

namespace frontend\business;


use common\models\Bill;
use common\components\SystemParamsUtil;
use common\models\User;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BalanceSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BillRecordSaveForPayBack;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BorrowFundSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BusinessLogSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\FundSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\MessageSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\UserActiveSaveByTrans;
use yii\base\Exception;
use yii\log\Logger;

class BillUtil
{
    /**
     * 设置坏账
     * @param $billInfo
     * @param $user
     * @param $error
     */
    public static function SetBadRemark($billInfo,$user,&$error)
    {
        $error = '';
        //业务日志、修改提现记录状态、给用户消息
        $transActions = [];
        if(!($billInfo instanceof Bill))
        {
            $error = '不是账单记录';
            return false;
        }
        if(!($user instanceof User))
        {
            $error = '不是后台用户';
            return false;
        }
        if($billInfo->status > 0)
        {
            $error = '账单状态错误';
            return false;
        }
        $billInfo->status = 2;
        $transActions[] = new BillRecordSaveForPayBack($billInfo,['error'=>'设置账单坏账异常']);
        $businessLog = BusinessLogUtil::GetBusinessLogNewForBackend(268, $user);
        $businessLog->remark5 = strval($billInfo->bill_id);
        $businessLog->remark6 = strval($billInfo->borrow_fund_id);
        $businessLog->remark7 = $billInfo->bad_bill_remark;
        $businessLog->remark9 = sprintf('%s将借款单号【%s】的第【%s】期账单设置成坏账，该借款单一共【%s】期',
            $user->username,
            $billInfo->borrow_fund_id,
            $billInfo->cur_stage,
            $billInfo->by_stages_count);
        $transActions[] = new BusinessLogSaveForReward($businessLog,['error'=>'美愿基金借款账单设置成坏账业务日志存储失败']);

        $msgContent = sprintf('您的借款单号【%s】的第【%s】期已经被设置成坏账，将严重影响您在社会上的诚信，请尽快还清！',
            $billInfo->borrow_fund_id,
            $billInfo->cur_stage);
        $msg  = MessageUtil::GetMsgNewModel('75',$msgContent,$billInfo->user_id);
        $transActions[] = new MessageSaveForReward($msg);
        if(!RewardUtil::RewardSaveByTransaction($transActions, $error))
        {
            return false;
        }
        return true;
    }
    /**
     * 生成还款账单不保存，返回记录集合
     * @param $borrowRecord
     */
    public static function GetPayBackBills($borrowRecord)
    {
        $billList = [];
        $byStageCount = $borrowRecord->by_stages_count;
        $stage_money = $borrowRecord->stage_money;
        $borrowMoney = $borrowRecord->borrow_money;
        $everySourceMoney = round($borrowMoney/($byStageCount * 1.00),2);//每期本金
        $everyBorrowFee = $stage_money - $everySourceMoney;//每期手续费
        $start_date = date('Y-m-d');
        for($i = 1; $i <= $byStageCount; $i ++)
        {
            $bill = new Bill();
            $bill->borrow_fund_id = $borrowRecord->borrow_fund_id;
            $bill->user_id = $borrowRecord->user_id;
            $bill->back_fee = $stage_money;
            $bill->source_fee = $everySourceMoney;
            $bill->borrow_fee = $everyBorrowFee;
            $start_date = date('Y-m-d',strtotime($start_date .' +1 months'));
            $bill->back_date = $start_date;
            $bill->status = 0;
            $bill->by_stages_count = $borrowRecord->by_stages_count;
            $bill->cur_stage = $i;
            $bill->pay_times = 0;
            $bill->create_time = date('Y-m-d H:i:s');
            $bill->is_cur_stage = ($i === 1 ? 1:0);
            $bill->real_back_fee = 0;
            $bill->breach_fee = 0;
            $bill->last_breach_fee = 0;
            $bill->breach_days = 0;
            $bill->is_check_delay = 1;
            $bill->is_delay = 1;
            $billList[] = $bill;
        }
        return $billList;
    }

    /**
     * 支付宝支付还款处理
     * @param $passParams
     * @param $billRecord
     * @param $borrowRecord
     * @param $user
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function PayBackByAlipay($passParams,$billRecord,$borrowRecord,$user,&$error)
    {
        $transActions = [];

        $userActive = UserActiveUtil::GetUserActiveByUserId($user->account_id);
        if(!isset($userActive))
        {
            $error = '活跃度信息不存在';
            return false;
        }
        $transActions[] = new UserActiveSaveByTrans($userActive,['modify_type'=>'back_bill','fund_back_money'=> $passParams['real_back_money']]);

        $borrowRecordId = $borrowRecord->borrow_fund_id;
        $businessLog = BusinessLogUtil::GetBusinessLogNew(2,$user);
        $businessLog->remark5 = strval($borrowRecord->borrow_fund_id);
        $businessLog->remark6 = strval($billRecord->bill_id);
        $businessLog->remark9 = sprintf('%s用支付宝支付还了借款【%s】，第【%s】期',$user->nick_name,$passParams['real_back_money'],$billRecord->cur_stage);
        $transActions[] = new BusinessLogSaveForReward($businessLog,['error'=>'支付宝支付还款业务日志存储失败']);

        $billRecord->pay_type = 3;
        $billRecord->status = 1;
        $billRecord->back_time =date('Y-m-d H:i:s');
        $billRecord->real_back_fee = $passParams['real_back_money'];
        $billRecord->breach_fee = $passParams['breach_money'];
        $billRecord->last_breach_fee = $passParams['last_breach_fee'];
        $billRecord->breach_days = $passParams['breach_days'];
        $transActions[] = new BillRecordSaveForPayBack($billRecord,['error'=>'还款账单信息存储失败']);
        if($billRecord->by_stages_count == $billRecord->cur_stage)
        {
            $borrowRecord->is_back = 1;
            $borrowRecord->back_time = date('Y-m-d H:i:s');
            $transActions[] = new BorrowFundSaveForReward($borrowRecord,[]);
        }
        else
        {
            //激活下期账单
            $nextBillRecord = self::GetNextStageBillRecord($borrowRecord->borrow_fund_id, $user->account_id, $billRecord->cur_stage + 1);
            if(!isset($nextBillRecord))
            {
                $error = '下期账单无法找到';
                \Yii::getLogger()->log($error.' 借款单id：'.strval($borrowRecord->borrow_fund_id).' 期数：'.strval($billRecord->cur_stage + 1),Logger::LEVEL_ERROR);
                return false;
            }
            $nextBillRecord->is_cur_stage = 1;
            $transActions[] = new BillRecordSaveForPayBack($nextBillRecord,['error'=>'下期账单信息激活']);
            $borrowRecord = null;//无需更新借款单据所有设置成null
        }
        //美愿基金金额 还原
        $fund = FundUtil::GetFundByUserId($user->account_id);
        if(!isset($fund))
        {
            $error = '美愿基金信息不存在';
            return false;
        }
        if(doubleval($billRecord->source_fee) <= 0)
        {
            $error = '本金金额为零，数据异常';
            return false;
        }
        $sourceBalance = $fund->credit_balance;
        $fund->credit_balance += $billRecord->source_fee;
        $transActions[] = new FundSaveForReward($fund);
        $businessLog1 = BusinessLogUtil::GetBusinessLogNew(269,$user);
        $businessLog1->remark5 = strval($borrowRecord->borrow_fund_id);
        $businessLog1->remark6 = $billRecord->bill_id;
        $businessLog1->remark7 = strval($fund->fund_id);
        $businessLog1->remark9 = sprintf('%s用账户余额还款,美愿基金可用刻度增加，美愿基金可用额度增加【%s】元，增加前美愿基金可用额度【%s】元',
            $user->nick_name,
            $billRecord->source_fee,
            $sourceBalance);
        $transActions[] = new BusinessLogSaveForReward($businessLog1,['error'=>'美愿基金额度恢复业务日志存储失败']);

        $msgContent = sprintf('对借款单【%s】，进行了第【%s】期还款，共【%s】期',$borrowRecordId,$billRecord->cur_stage,$billRecord->by_stages_count);
        $msg = MessageUtil::GetMsgNewModel(67,$msgContent,$user->account_id);
        $transActions[] = new MessageSaveForReward($msg,[]);

        if(!RewardUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }

        return true;
    }

    /**
     * 账户余额还款
     * @param $passParams
     * @param $billRecord
     * @param $borrowRecord
     * @param $billInfo
     * @param $loginUserId
     * @param $error
     */
    public static function PayBackBillByAccountBalance($passParams, $billRecord, $borrowRecord,$billInfo,$user,&$error)
    {
        $transActions = [];
        $userActive = UserActiveUtil::GetUserActiveByUserId($user->account_id);
        if(!isset($userActive))
        {
            $error = '活跃度信息不存在';
            return false;
        }
        $transActions[] = new UserActiveSaveByTrans($userActive,['modify_type'=>'back_bill','fund_back_money'=> $passParams['real_back_money']]);
        $borrowRecordId = $borrowRecord->borrow_fund_id;
        //账单记录、计算违约金额、业务日志、查看是否还清，如果还清则更新借款单单据
        $transActions[] = new BalanceSaveForReward($billInfo,['modify_type'=>'sub_balance','sub_money'=>$passParams['pay_money']]);


        $businessLog = BusinessLogUtil::GetBusinessLogForPayBackBill($passParams, $billInfo,$borrowRecord,$billRecord,$user);
        $transActions[] = new BusinessLogSaveForReward($businessLog,['error'=>'余额还款业务日志存储失败']);

        $billRecord->pay_type = 1;
        $billRecord->status = 1;
        $billRecord->back_time =date('Y-m-d H:i:s');
        $billRecord->real_back_fee = $passParams['real_back_money'];
        $billRecord->breach_fee = $passParams['breach_money'];
        $billRecord->last_breach_fee = $passParams['last_breach_fee'];
        $billRecord->breach_days = $passParams['breach_days'];
        $transActions[] = new BillRecordSaveForPayBack($billRecord,['error'=>'还款账单信息存储失败']);
        if($billRecord->by_stages_count == $billRecord->cur_stage)
        {
            $borrowRecord->is_back = 1;
            $borrowRecord->back_time = date('Y-m-d H:i:s');
            $transActions[] = new BorrowFundSaveForReward($borrowRecord,[]);
        }
        else
        {
            //激活下期账单
            $nextBillRecord = self::GetNextStageBillRecord($borrowRecord->borrow_fund_id, $user->account_id, $billRecord->cur_stage + 1);
            if(!isset($nextBillRecord))
            {
                $error = '下期账单无法找到';
                \Yii::getLogger()->log($error.' 借款单id：'.strval($borrowRecord->borrow_fund_id).' 期数：'.strval($billRecord->cur_stage + 1),Logger::LEVEL_ERROR);
                return false;
            }
            $nextBillRecord->is_cur_stage = 1;
            $transActions[] = new BillRecordSaveForPayBack($nextBillRecord,['error'=>'下期还还款账单激活失败']);
            $borrowRecord = null;//无需更新借款单据所有设置成null
        }
        //美愿基金金额 还原
        $fund = FundUtil::GetFundByUserId($user->account_id);
        if(!isset($fund))
        {
            $error = '美愿基金信息不存在';
            return false;
        }
        if(doubleval($billRecord->source_fee) <= 0)
        {
            $error = '本金金额为零，数据异常';
            return false;
        }
        $sourceBalance = $fund->credit_balance;
        $fund->credit_balance += $billRecord->source_fee;
        $transActions[] = new FundSaveForReward($fund);
        $businessLog1 = BusinessLogUtil::GetBusinessLogNew(269,$user);
        $businessLog1->remark5 = strval($borrowRecord->borrow_fund_id);
        $businessLog1->remark6 = $billRecord->bill_id;
        $businessLog1->remark7 = strval($fund->fund_id);
        $businessLog1->remark9 = sprintf('%s用账户余额还款,美愿基金可用刻度增加，美愿基金可用额度增加【%s】元，增加前美愿基金可用额度【%s】元',
            $user->nick_name,
            $billRecord->source_fee,
            $sourceBalance);
        $transActions[] = new BusinessLogSaveForReward($businessLog1,['error'=>'美愿基金额度恢复业务日志存储失败']);

        $msgContent = sprintf('对借款单【%s】，进行了第【%s】期还款，共【%s】期',$borrowRecordId,$billRecord->cur_stage,$billRecord->by_stages_count);
        $msg = MessageUtil::GetMsgNewModel(67,$msgContent,$user->account_id);
        $transActions[] = new MessageSaveForReward($msg,[]);

        if(!RewardUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }
        return true;
    }

    public static function GetNextStageBillRecord($borrow_id,$user_id,$stage_no)
    {
        return Bill::findOne([
            'borrow_fund_id'=>$borrow_id,
            'user_id'=>$user_id,
            'cur_stage'=>$stage_no,
            'is_cur_stage'=>'0'
        ]);
    }
    /**
     * 格式化还款记录,账单详情显示
     * @param $recrodList
     * @return array
     */
    public static function GetFormateBillListForBillDetail($recrodList)
    {
        $out = [];
        if(empty($recrodList))
        {
            return $out;
        }
        foreach($recrodList as $one)
        {
            $ary = [
                'back_time' =>$one->back_time,
                'back_money'=>$one->real_back_fee
            ];
            $out[] = $ary;
        }
        return $out;
    }

    /**
     * 根据id获取账单信息
     * @param $bill_id
     * @return null|static
     */
    public static function GetBillRecordById($bill_id)
    {
        return Bill::findOne([
            'bill_id'=>$bill_id
        ]);
    }

    /**
     * 根据借款单据获取所有已还账单信息
     * @param $borrow_id
     */
    public static function GetBillListByBorrowId($borrow_id, $user_id)
    {
        return Bill::find()->orderBy('cur_stage asc')->where([
            'and',['borrow_fund_id'=>$borrow_id, 'status'=> '1','user_id'=>$user_id]
        ])->all();
    }

    /**
     * 根据状态获取账单
     * @param $flag
     * @param $start_id
     * @param $status
     */
    public static function GetBillList($flag, $start_id, $status,$user_id)
    {
        $conditon = ['and',['user_id'=>$user_id,'status'=>$status,'is_cur_stage'=>'1']];
        switch($flag)
        {
            case 'up':
                $conditon[]= ['>','borrow_fund_id',$start_id];
                break;
            case 'down':
                $conditon[]= ['<','borrow_fund_id',$start_id];
                break;
            default:
                break;
        }
        $rcList = Bill::find()->limit(10)->orderBy('bill_id desc')->where($conditon)->all();
        return $rcList;
    }

    /**
     * 格式化账单输出
     * @param $billList
     */
    public static function GetFormateBillList($billList)
    {
        $out = [];
        if(empty($billList))
        {
            return $out;
        }
        foreach($billList as $bill)
        {
            $ary = [
                'bill_id'=>$bill->bill_id,
                'back_date'=>$bill->back_date,
                'left_stage'=> $bill->by_stages_count - $bill->cur_stage,
                'back_fee'=> $bill->back_fee
            ];
            $out[] = $ary;
        }
        return $out;
    }

    /**
     * 根据账单信息获取违约金额
     * @param $billModel 账单信息
     * @param $borrowModel  借款单据
     * @return array 还款总金额 违约金额 和 持续违约金额 违约天数
     */
    public static function GetBillMoney($billModel,$borrowModel)
    {
        $real_back_money = doubleval($billModel->back_fee);
        $curdate =date('Y-m-d');
        $back_date = $billModel->back_date;
        $dis = strtotime($curdate) - strtotime($back_date);
        $delayDays = 0;
        if($dis > 0)
        {
            $delayDays = intval($dis / (3600.0 * 24),0);
        }
        $lastDelayDays = intval(SystemParamsUtil::GetSystemParam('system_days_breach_to_lastbreach',true));
        $breachMoney = 0.0;
        if($delayDays > 0)
        {
            $breachMoney = doubleval($billModel->back_fee) * doubleval($borrowModel->breach_rate);
        }
        $lastBreachMoney = 0.0;
        if($delayDays > $lastDelayDays)
        {
            $lastBreachMoney = ($lastDelayDays - $delayDays) * doubleval($borrowModel->breach_last_rate);
        }
        $real_back_money += $breachMoney + $lastBreachMoney;
        $out = [
            'real_back_money' => strval($real_back_money),
            'breach_money' => strval($breachMoney),
            'last_breach_money' => strval($lastBreachMoney),
            'breach_days' => strval($delayDays)
        ];
        return $out;
    }
} 