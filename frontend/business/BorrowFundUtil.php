<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/23
 * Time: 15:29
 */

namespace frontend\business;


use backend\business\UserUtil;
use common\models\BorrowFund;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BorrowFundSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BusinessLogSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\MessageSaveForReward;

class BorrowFundUtil
{
    /**
     * 美愿基金打款
     * @param $borrowFund
     * @param $user_id
     * @param $error
     */
    public static function SetFinaceOk($borrowFund,$user_id,&$error)
    {
        $error = '';
        //业务日志、修改提现记录状态、给用户消息
        $transActions = [];
        if(!($borrowFund instanceof BorrowFund))
        {
            $error = '不是余额提现记录';
            return false;
        }
        if($borrowFund->status_result > 2)
        {
            $error = '已经审核，无需再审核';
            return false;
        }
        $borrowFund->status_result = 4;
        $transActions[] = new BorrowFundSaveForReward($borrowFund);
        $user = UserUtil::GetUserByUserId($user_id);
        if(!isset($user))
        {
            $error = '后台人员信息不存在';
            return false;
        }
        $businessLog = BusinessLogUtil::GetBusinessLogNewForBackend(267, $user);
        $businessLog->remark5 = strval($borrowFund->borrow_fund_id);
        $businessLog->remark6 = strval($borrowFund->user_id);
        $businessLog->remark7 = $borrowFund->user_name;
        $businessLog->remark9 = sprintf('%s对【%s】进行了美愿基金借款单打款，借款金额【%s】，打款金额【%s】，银行卡号【%s】，身份证号【%s】',
            $user->username,
            $borrowFund->user_name,
            $borrowFund->borrow_money,
            $borrowFund->borrow_money,
            $borrowFund->card_no,
            $borrowFund->identity_no);
        $transActions[] = new BusinessLogSaveForReward($businessLog,['error'=>'美愿基金额借款单财务打款设置业务日志存储失败']);

        $msgContent = sprintf('您的美愿基金提现金额已经打入尾号【%s】的银行卡，金额【%s】，请查收！',
            substr($borrowFund->card_no,strlen($borrowFund->card_no)-4),
            $borrowFund->borrow_money);
        $msg  = MessageUtil::GetMsgNewModel('75',$msgContent,$borrowFund->user_id);
        $transActions[] = new MessageSaveForReward($msg);
        if(!RewardUtil::RewardSaveByTransaction($transActions, $error))
        {
            return false;
        }
        return true;
    }
    /**
     * 根据id获取美愿基金借款账单
     * @param $borrow_id
     */
        public static function GetBorrowFundRecordById($borrow_id)
        {
            return BorrowFund::findOne([
                'borrow_fund_id'=>$borrow_id
            ]);
        }
} 