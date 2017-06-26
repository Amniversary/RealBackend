<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/22
 * Time: 14:16
 */

namespace frontend\business;


use common\components\Des3Crypt;
use common\components\StatusUtil;
use common\components\SystemParamsUtil;
use common\models\BorrowFund;
use yii\base\Exception;
use \yii\log\Logger;

class LoanUtil
{

    /**
     * 获取借款协议
     * @param $borrowRecord
     * @param $error
     */
    public static function GetLoanProtocalNew($borrowRecord,&$url,&$error)
    {
        $url = '';
        if(!($borrowRecord instanceof BorrowFund))
        {
            $error = '不是借款对象';
            return false;
        }
        $baseCerification = BaseCerificationUtil::GetBaseCertificationInfoByUserId($borrowRecord->user_id);
        if(!isset($baseCerification))
        {
            $error = '基础认证信息不存在';
            return false;
        }
        $params = [
            'borrow_money'=>$borrowRecord->borrow_money,
            'by_stages_count'=>$borrowRecord->by_stages_count,
            'borrow_fee'=>round(doubleval($borrowRecord->borrow_money)*doubleval($borrowRecord->borrow_rate)/100.0, 2),
            'stage_money'=>'220'
        ];
        $domain = \Yii::$app->params['FrontDomain'];
        $url = LoanUtil::GetLoanProtocal($params,$baseCerification,true,$domain);
        return true;
    }
    /**
     * 获取借款协议
     * @param $params
     * @param $fund
     * @param $baseCertification
     * @param $user
     * @param $error
     */
    public static function GetLoanProtocal($params,$baseCertification,$is_check=false,$domain='')
    {
        $key = \Yii::$app->params['protocal_crypt_key'];
        $dataAry=[
            'borrow_money'=>$params['borrow_money'],
            'identity_no'=>$baseCertification['identity_no'],
            'real_name'=>$baseCertification['user_name'],
            'service_fee'=>$params['borrow_fee'],
            'loan_months'=>$params['by_stages_count'],
            'start_date'=>(!$is_check?'':date('Y-m-d')),
        ];
        $str = serialize($dataAry);
        $encodeStr = Des3Crypt::des_encrypt($str,$key);
        $encodeStr = urlencode($encodeStr);
        if(empty($domain))
        {
            $domain = $_SERVER['HTTP_HOST'];
        }
        $url = 'http://'.$domain.'/mywish/borrowprotocal?data='.$encodeStr;
        return $url;
    }
/**
 * 获取美愿基金借款参数
 * @param $halfYearDelayCount
 * @param $user_type  1 学生  2社会人员
 */
    public static function GetLoadParams($halfYearDelayCount,$fund,$user_type,&$outParams,&$error)
    {
        $outParams = [];
        $error = '';
        $fundCrediteMoney = doubleval($fund->credit_money);
        $baseFund = doubleval($fund->credit_balance);//取出信用余额
        $sysParams = SystemParamsUtil::GetFundParams();
        $halfYearDelayCount = intval($halfYearDelayCount);
        $delayTimesForRate = $sysParams['system_fund_rate_for_halfdelaytimes'];
        $delayTimesForCrediteFee = $sysParams['system_fund_credite_value_for_halfdelaytimes'];
        $delayTimesForRateItems = explode('-',$delayTimesForRate);
        $delayTimesForCrediteFeeItems = explode('-',$delayTimesForCrediteFee);
        $lenRate = count($delayTimesForRateItems);
        $lenFee = count($delayTimesForCrediteFeeItems);
        if($lenFee !== $lenRate)
        {
            $error = '借款手续费系统设置参数错误1';
            return false;
        }
        $maxCount = $lenRate;
        if($maxCount < $halfYearDelayCount)
        {
            $error = '半年内拖欠次数太多，不予借款';
            return false;
        }
        switch($user_type)
        {
            case 1:
                $borrowRate = $sysParams['system_stu_borrow_by_stages_rate'];
                break;
            case 2:
                $borrowRate = $sysParams['system_social_borrow_by_stages_rate'];
                break;
                default:
                    $error ='用户类型异常';
                return false;
        }
        $borrowRateItems = explode('-',$borrowRate);
        $lenBorrowRateitems = count($borrowRateItems);
        $maxBorrowTimes = intval($sysParams['system_fund_by_stages_count']);
        if($lenBorrowRateitems !== $maxBorrowTimes)
        {
            $error = '参数错误2';
            return false;
        }
        $extendRate = $halfYearDelayCount > 0 ? doubleval($delayTimesForRateItems[$halfYearDelayCount - 1]) : 0;
        $extendFee = $halfYearDelayCount > 0 ? doubleval($delayTimesForCrediteFeeItems[$halfYearDelayCount -1]): 0;
        $baseFund = $baseFund - $fundCrediteMoney * $extendFee / 100;
        if($baseFund <= 0)
        {
            $error = '信用余额不足，不能借款';
            return false;
        }
        $borrowRateList = [];
        if($extendRate > 0)
        {
            for($j =0; $j < count($borrowRateItems); $j++)
            {
                $borrowRateList[] = strval(doubleval($borrowRateItems[$j]) + $extendRate);
            }
        }
        else
        {
            $borrowRateList = $borrowRateItems;
        }
        $borrowRate = implode('-',$borrowRateList);
        $outParams = [
            'by_stages_unit'=>$sysParams['system_fund_time_unit'],
            'by_stages_fee'=>$borrowRate,
            'max_by_stages_count'=>strval($maxBorrowTimes),
            'max_money'=>strval($baseFund),
            'breach_rate'=>$sysParams['system_breach_rate'],
            'breach_last_rate'=>$sysParams['system_last_breach_rate'],
            'half_year_delay_times'=>strval($halfYearDelayCount)
        ];
        return true;
    }

    /**
     * 获取借款记录模型
     */
    public static function GetBorrowFundRecordModel($params,$bandCard,$user)
    {
        $model = new BorrowFund();
        $model->is_back = 0;
        $model->status_result = 1;
        $model->finance_has_paid = 0;
        $model->borrow_type = 2;
        $model->user_id = $user->account_id;
        $model->stage_money = doubleval($params['stage_money']);
        $model->borrow_money = $params['borrow_money'];
        $model->by_stages_count = $params['by_stages_count'];
        $model->borrow_rate = $params['borrow_rate'];
        $model->breach_rate = $params['breach_rate'];
        $model->breach_last_rate = $params['breach_last_rate'];
        $model->half_delay_times = $params['half_year_delay_times'];
        $model->create_time = date('Y-m-d H:i:s');
        $model->user_name = $bandCard->user_name;
        $model->card_no = $bandCard->card_no;
        $model->identity_no = $bandCard->identity_no;
        $model->bank_name = $bandCard->bank_name;
        $model->remark1 = strval($bandCard->user_bank_card_id);
        return $model;
    }


    /**
     * 美愿基金体现
     * @param $params  外部接口传递过来的参数
     * @param $fund   美元基金记录
     * @param $bandCard  银行卡记录
     * @param $user  用户信息
     * @param $error 返回的错误信息
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function MeiYuanLoad($params, $fund,$bandCard, $user, &$error)
    {
        $error = '';
        //借款单、账单（审核通过后产生）、操作日志、基金扣除（审核失败退回）、审核单
        $borrowRecord = self::GetBorrowFundRecordModel($params,$bandCard,$user);
        $businessLog = BusinessLogUtil::GetBusinessLogForFundBorrow($params,$fund,$user);
        $fund->credit_balance = strval(doubleval($fund ->credit_balance) -doubleval($params['borrow_money']));
        if($fund->credit_balance < 0)
        {
            $error = '美愿基金额度不足';
            return false;
        }
        $fund->cashing_sum = strval(doubleval($fund ->cashing_sum) +doubleval($params['borrow_money']));
        $checkRecord = BusinessCheckUtil::GetBusinessCheckModelNew('3','',$user);
        $trans = \Yii::$app->db->beginTransaction();
        try
        {
            if(!$borrowRecord->save())
            {
                \Yii::getLogger()->log(var_export($borrowRecord->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('借款单据保存失败');
            }
            $borrow_id = $borrowRecord->borrow_fund_id;
            $businessLog->remark5 = strval($borrow_id);
            if(!$businessLog->save())
            {
                \Yii::getLogger()->log(var_export($businessLog->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('借款日志存储失败');
            }
            if(!$fund->save())
            {
                \Yii::getLogger()->log(var_export($fund->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('美元基金余额更新失败');
            }
            $checkRecord->relate_id = $borrow_id;
            if(!$checkRecord->save())
            {
                \Yii::getLogger()->log(var_export($fund->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('借款审核记录生成失败');
            }
            $trans->commit();
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $trans->rollBack();
            return false;
        }
        return true;
    }


    public static function GetBorrowRecordList($flag,$start_id,$status,$user_id)
    {
        $statusList = StatusUtil::GetStatusList($status,4);
        $conditon =['and',['user_id'=>$user_id],['in','status_result',$statusList]];// 'user_id=:uid and status_result in (:statusList)';
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
        $rcList = BorrowFund::find()->limit(10)->where($conditon)->orderBy('borrow_fund_id desc')->all();
        return $rcList;
    }

    /**
     * 格式化借款记录输出
     * @param $recordList
     */
    public static function GetFormateBorrowRecord($recordList)
    {
        $out = [];
        if(empty($recordList))
        {
            return $out;
        }
        foreach($recordList as $one)
        {
            $len = strlen($one->card_no);
            $ary = [
                'borrow_fund_id'=>$one->borrow_fund_id,
                'borrow_money'=>$one->borrow_money,
                'borrow_rate'=>$one->borrow_rate,
                'create_time'=>$one->create_time,
                'bank_name'=> $one->bank_name,
                'card_no_last4'=>(empty($one->card_no)?'':substr($one->card_no,$len-4))
            ];

            $out[] = $ary;
        }

        return $out;
    }
} 