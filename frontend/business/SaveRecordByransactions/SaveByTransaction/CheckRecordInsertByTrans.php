<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\BusinessCheck;
use common\models\TicketToCash;
use frontend\business\BusinessCheckUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use frontend\business\TicketToCashUtil;
use yii\log\Logger;


/**
 * 审核记录操作
 */
class CheckRecordInsertByTrans  implements ISaveForTransaction
{
    private $all_params;

    /**
     * @param $TicketToCash
     * @param $all_params
     * @param $all_params
     */
    public function __construct($all_params)
    {
        $this->all_params = $all_params;
    }

    public function SaveRecordForTransaction(&$error,&$outInfo){

        $tickettocash = TicketToCashUtil::GetTicketToCashById($this->all_params['record_id']);
        $tickettocash->check_time = date('Y-m-d H:i:s');
        $tickettocash->refuesd_reason = $this->all_params['refuesd_reason'];
        $tickettocash->finance_remark = $this->all_params['finance_remark'];
        $tickettocash->status = $this->all_params['check_rst'];
        $tickettocash->finace_ok_time = $this->all_params['finace_ok_time'];
        // 票提现表mb_ticket_to_cash 数据更新
        if(!$tickettocash->save()){
            \Yii::getLogger()->log('all_params=:'.var_export($tickettocash->getError(),true),Logger::LEVEL_ERROR);
            $error = '票提现数据更新失败';
            return false;
        }

        $businesscheck = BusinessCheckUtil::GetBusinessCheckByRelate_id($this->all_params['record_id']);
        $businesscheck->business_type =  1;
        $businesscheck->status =  1;
        $businesscheck->check_result_status =  $this->all_params['check_result_status'];
        $businesscheck->check_time =  date('Y-m-d H:i:s');
        $businesscheck->check_user_id =  $this->all_params['check_user_id'];
        $businesscheck->check_user_name =  $this->all_params['check_user_name'];
        $businesscheck->refused_reason =  $this->all_params['refuesd_reason'];

        //审核表：mb_business_check 数据操作
        if(!$businesscheck->save()){
            \Yii::getLogger()->log('all_params=:'.var_export($tickettocash->attributes,true),Logger::LEVEL_ERROR);
            $error = '票提现记录生成失败';
            return false;
        }

        return true;
    }


}