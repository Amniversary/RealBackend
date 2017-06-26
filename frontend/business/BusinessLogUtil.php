<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/21
 * Time: 15:01
 */

namespace frontend\business;
use common\components\Des3Crypt;
use common\models\BusinessLog;

class BusinessLogUtil
{

    /**
     * 支付宝支付获取业务日志
     * @param $rewardInfo
     * @param $wish
     */
    public static function GetBusinessLogForAlipayReward($rewardInfo,$wish)
    {
        $modifyPwdLog = new BusinessLog();
        $modifyPwdLog->operate_type = 259; //中级认证
        $modifyPwdLog->remark1 = $rewardInfo->reward_money;
        $modifyPwdLog->remark3 =  strval($rewardInfo->reward_user_id);
        $modifyPwdLog->remark4 = $rewardInfo->reward_user_name;
        $modifyPwdLog->remark5 = strval($wish->wish_id);
        $modifyPwdLog->remark6 = $wish->wish_name;
        //$modifyPwdLog->remark7 = strval($fund->fund_id);
        $modifyPwdLog->remark9 = sprintf('%s通过支付宝支付打赏了愿望【%s】，打赏总金额【%s】，支付宝打赏金额【%s】,红包金额【%s】,打赏前除红包金额【%s】，打赏前红包金额【%s】',
            $rewardInfo->reward_user_name,
            $wish->wish_name,
            $rewardInfo->reward_money,
            $rewardInfo->reward_money_except_packets,
            $rewardInfo->red_packets_money,
            $wish->ready_reward_money,
            $wish->red_packets_money);
        $modifyPwdLog->remark13 = date('Y-m-d H:i:s',time());
        return $modifyPwdLog;
    }
    /**
     * 获取还款日志
     * @param $billInfo
     * @param $user
     * @return BusinessLog
     */
    public static function GetBusinessLogForPayBackBill($params,$billInfo,$borrowRecord,$billRecord,$user)
    {
        $modifyPwdLog = new BusinessLog();
        $modifyPwdLog->operate_type = 2;
        $modifyPwdLog->remark1 = $params['pay_money'];
        $modifyPwdLog->remark3 = strval($user->account_id);
        $modifyPwdLog->remark4 = $user->nick_name;
        $modifyPwdLog->remark5 = strval($borrowRecord->borrow_fund_id);
        $modifyPwdLog->remark6 = strval($billRecord->bill_id);
        $modifyPwdLog->remark7 = strval($billInfo->account_info_id);
        $modifyPwdLog->remark9 = sprintf('%s用账户余额还了借款【%s】，第【%s】期',$user->nick_name,$params['pay_money'],$billRecord->cur_stage);
        $modifyPwdLog->remark13 = date('Y-m-d H:i:s',time());
        $str = var_export($billInfo->attributes,true);
        $key = \Yii::$app->params['log_encrypt_key'];
        $decodeStr = Des3Crypt::des_encrypt($str, $key);
        $modifyPwdLog->remark10 = $decodeStr;
        return $modifyPwdLog;
    }

    /**
     * 获取业务日志模型
     * @param $type 业务日志类别
     * @param $user
     * @return BusinessLog
     */
    public static function GetBusinessLogNew($type,$user)
    {
        $modifyPwdLog = new BusinessLog();
        $modifyPwdLog->operate_type = $type;
        $modifyPwdLog->remark3 = strval($user->account_id);
        $modifyPwdLog->remark4 = $user->nick_name;
        $modifyPwdLog->remark13 = date('Y-m-d H:i:s',time());
        return $modifyPwdLog;
    }

    /**
     * 后台获取业务日志模型
     * @param $type 业务日志类别
     * @param $user
     * @return BusinessLog
     */
    public static function GetBusinessLogNewForBackend($type,$user)
    {
        $modifyPwdLog = new BusinessLog();
        $modifyPwdLog->operate_type = $type;
        $modifyPwdLog->remark3 = strval($user->backend_user_id);
        $modifyPwdLog->remark4 = $user->username;
        $modifyPwdLog->remark13 = date('Y-m-d H:i:s',time());
        return $modifyPwdLog;
    }

    /**
     * 提现业务日志
     * @param $cashModel
     * @param $user
     * @param $billInfo
     */
    public static function GetBusinessLogForGetCash($cashModel,$user,$billInfo)
    {
        $modifyPwdLog = new BusinessLog();
        $modifyPwdLog->operate_type = 2;
        $modifyPwdLog->remark1 = $cashModel['cash_money'];
        $modifyPwdLog->remark3 =  strval($user->account_id);
        $modifyPwdLog->remark4 = $user->nick_name;
        //$modifyPwdLog->remark5 = $borrowRecord->borrow_fund_id;
        //$modifyPwdLog->remark6 = $billInfo->bill_id;
        $modifyPwdLog->remark7 = strval($billInfo->account_info_id);
        $modifyPwdLog->remark9 = sprintf('%s对账户余额进行了提现，提现金额为【%s】',$user->nick_name,$cashModel->cash_money);
        $modifyPwdLog->remark13 = date('Y-m-d H:i:s',time());
        $str = var_export($billInfo->attributes,true);
        $key = \Yii::$app->params['log_encrypt_key'];
        $decodeStr = Des3Crypt::des_encrypt($str, $key);
        $modifyPwdLog->remark10 = $decodeStr;
        return $modifyPwdLog;
    }

    /**
     * 获取修改密码业务日志
     * @param $billAccount
     * @param $user
     */
    public static function GetBusinessLogForModifyPwd($billInfo,$user)
    {
        $modifyPwdLog = new BusinessLog();
        $modifyPwdLog->operate_type = 1;
        //$modifyPwdLog->remark1 = $pay_money;
        $modifyPwdLog->remark3 =  strval($user->account_id);
        $modifyPwdLog->remark4 = $user->nick_name;
        //$modifyPwdLog->remark5 = $wishRecord->wish_id;
        //$modifyPwdLog->remark6 = $wishRecord->wish_name;
        $modifyPwdLog->remark7 = strval($billInfo->account_info_id);
        $modifyPwdLog->remark9 = sprintf('%s修改了支付密码',$user->nick_name);
        $modifyPwdLog->remark13 = date('Y-m-d H:i:s',time());
        $str = var_export($billInfo->attributes,true);
        $key = \Yii::$app->params['log_encrypt_key'];
        $decodeStr = Des3Crypt::des_encrypt($str, $key);
        $modifyPwdLog->remark10 = $decodeStr;
        return $modifyPwdLog;
    }

    /**
     * 获取重置余额支付密码业务日志模型
     * @param $billInfo
     * @param $user
     * @return BusinessLog
     */
    public static function GetBusinessLogForResetPwd($billInfo, $user)
    {
        $modifyPwdLog = new BusinessLog();
        $modifyPwdLog->operate_type = 1;
        //$modifyPwdLog->remark1 = $pay_money;
        $modifyPwdLog->remark3 =  strval($user->account_id);
        $modifyPwdLog->remark4 = $user->nick_name;
        //$modifyPwdLog->remark5 = $wishRecord->wish_id;
        //$modifyPwdLog->remark6 = $wishRecord->wish_name;
        $modifyPwdLog->remark7 = strval($billInfo->account_info_id);
        $modifyPwdLog->remark9 = sprintf('%s重置了支付密码',$user->nick_name);
        $modifyPwdLog->remark13 = date('Y-m-d H:i:s',time());
        $str = var_export($billInfo->attributes,true);
        $key = \Yii::$app->params['log_encrypt_key'];
        $decodeStr = Des3Crypt::des_encrypt($str, $key);
        $modifyPwdLog->remark10 = $decodeStr;
        return $modifyPwdLog;
    }

    /**
     * 美愿基金借款日志
     * @param $billInfo
     * @param $user
     * @return BusinessLog
     */
    public static function GetBusinessLogForFundBorrow($params,$fund, $user)
    {
        $modifyPwdLog = new BusinessLog();
        $modifyPwdLog->operate_type = 4; //借款
        //$modifyPwdLog->remark1 = $pay_money;
        $modifyPwdLog->remark3 =  strval($user->account_id);
        $modifyPwdLog->remark4 = $user->nick_name;
        //$modifyPwdLog->remark5 = $wishRecord->wish_id;
        //$modifyPwdLog->remark6 = $wishRecord->wish_name;
        $modifyPwdLog->remark7 = strval($fund->fund_id);
        $modifyPwdLog->remark9 = sprintf('%s从美愿基金借了【%s】,分为%s期，每期还款金额【%s】',$user->nick_name,$params['borrow_money'],$params['by_stages_count'],$params['stage_money']);
        $modifyPwdLog->remark13 = date('Y-m-d H:i:s',time());

        return $modifyPwdLog;
    }

    /**
     * 初级认证日志
     * @param $addCridtMoney
     * @param $user
     * @param $fund
     * @return BusinessLog
     */
    public static function GetBusinessLogForBaseCertification($addCridtMoney,$user, $fund)
    {
        $modifyPwdLog = new BusinessLog();
        $modifyPwdLog->operate_type = 258; //初级认证
        $modifyPwdLog->remark1 = $addCridtMoney;
        $modifyPwdLog->remark3 =  strval($user->account_id);
        $modifyPwdLog->remark4 = $user->nick_name;
        //$modifyPwdLog->remark5 = $wishRecord->wish_id;
        //$modifyPwdLog->remark6 = $wishRecord->wish_name;
        $modifyPwdLog->remark7 = strval($fund->fund_id);
        $modifyPwdLog->remark9 = sprintf('%s通过了初级认证，美愿基金信用额度增加了【%s】,增加后的额度【%s】',$user->nick_name,$addCridtMoney,$fund->credit_money);
        $modifyPwdLog->remark13 = date('Y-m-d H:i:s',time());
        return $modifyPwdLog;
    }

    /**
     * 中级认证日志
     * @param $addCridtMoney
     * @param $user
     * @param $fund
     * @return BusinessLog
     */
    public static function GetBusinessLogForIntermediateCertification($addCridtMoney,$user, $fund)
    {
        $modifyPwdLog = new BusinessLog();
        $modifyPwdLog->operate_type = 259; //中级认证
        $modifyPwdLog->remark1 = $addCridtMoney;
        $modifyPwdLog->remark3 =  strval($user->account_id);
        $modifyPwdLog->remark4 = $user->nick_name;
        //$modifyPwdLog->remark5 = $wishRecord->wish_id;
        //$modifyPwdLog->remark6 = $wishRecord->wish_name;
        $modifyPwdLog->remark7 = strval($fund->fund_id);
        $modifyPwdLog->remark9 = sprintf('%s通过了中级认证，美愿基金信用额度增加了【%s】,增加后的额度【%s】',$user->nick_name,$addCridtMoney,$fund->credit_money);
        $modifyPwdLog->remark13 = date('Y-m-d H:i:s',time());
        return $modifyPwdLog;
    }
    /**
     * 奖励到愿望红包加入愿望金额
     * @param $wish
     * @param $user
     * @param $personRedPackets
     */
    public static function GetBusinessLogForRedPacketsToWish($wish,$user,$personRedPackets)
    {
        $modifyPwdLog = new BusinessLog();
        $modifyPwdLog->operate_type = 257; //奖励愿望加入到愿望金额
        $modifyPwdLog->remark1 = $personRedPackets->packets_money;
        $modifyPwdLog->remark3 =  strval($user->account_id);
        $modifyPwdLog->remark4 = $user->nick_name;
        $modifyPwdLog->remark5 = strval($wish->wish_id);
        $modifyPwdLog->remark6 = $wish->wish_name;
        //$modifyPwdLog->remark7 = $fund->fund_id;
        if($user->centification_level > 0)
        {
            $content =sprintf('%s打赏了愿望【%s】,愿望发布者【%s】获取到了愿望红包【%s】元，已经加入愿望金额,打赏前除红包金额【%s】，红包金额【%s】',
                $user->nick_name,
                $wish->wish_name,
                $wish->publish_user_name,
                $personRedPackets->packets_money,
                $wish->ready_reward_money,
                $wish->red_packets_money);
        }
        else
        {
            $content =sprintf('%s打赏了愿望【%s】,愿望发布者【%s】获取到了愿望红包【%s】元，打赏者未通过初级认证，红包金额未加入愿望金额',$user->nick_name,$wish->wish_name,$wish->publish_user_name,$personRedPackets->packets_money);
        }
        $modifyPwdLog->remark9 = $content;
        $modifyPwdLog->remark13 = date('Y-m-d H:i:s',time());

        return $modifyPwdLog;
    }

    /**
     * 领取到直接奖励到账户红包
     * @param $billInfo
     * @param $user
     * @param $personRedPackets
     */
    public static function GetBusinessLogForRedPackets($billInfo,$user,$personRedPackets)
    {
        $modifyPwdLog = new BusinessLog();
        $modifyPwdLog->operate_type = 256;//奖励红包加入到余额
        $modifyPwdLog->remark1 = $personRedPackets->packets_money;
        $modifyPwdLog->remark3 = strval($user->account_id);
        $modifyPwdLog->remark4 = $user->nick_name;
        //$modifyPwdLog->remark5 = $wishRecord->wish_id;
        //$modifyPwdLog->remark6 = $wishRecord->wish_name;
        $modifyPwdLog->remark7 = strval($billInfo->account_info_id);
        $modifyPwdLog->remark9 = sprintf('%s领取了【%s】元的奖励红包并加入余额,加入前余额【%s】',$user->nick_name,$personRedPackets->packets_money,$billInfo->balance);
        $modifyPwdLog->remark13 = date('Y-m-d H:i:s',time());
        $str = var_export($billInfo->attributes,true);
        $key = \Yii::$app->params['log_encrypt_key'];
        $decodeStr = Des3Crypt::des_encrypt($str, $key);
        $modifyPwdLog->remark10 = $decodeStr;
        return $modifyPwdLog;
    }
} 