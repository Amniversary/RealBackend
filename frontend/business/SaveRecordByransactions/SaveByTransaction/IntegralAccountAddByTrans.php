<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/3
 * Time: 21:28
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use backend\models\IntegralAccountSearch;
use frontend\business\IntegralAccountUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;

class IntegralAccountAddByTrans implements ISaveForTransaction
{
    private  $recharge = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws //Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->recharge = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error ,&$outInfo)
    {
        if(!IntegralAccountUtil::UpdateIntegralAccountToAdd($this->recharge['integral_account_id'],$this->recharge['user_id'],$this->recharge['device_type'],$this->recharge['operate_type'],$this->recharge['operateValue'],$error))
        {
            $error = var_export($error,true).'integral  data===:'.var_export($this->recharge,true);
            return false;
        }

        return true;
    }
}