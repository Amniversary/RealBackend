<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:48
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\UserAccountInfo;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use frontend\business\UserAccountInfoUtil;
use yii\base\Exception;
use yii\log\Logger;

class BalanceSaveForReward implements ISaveForTransaction
{
    private  $userBill = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->userBill = $record;
        $this->extend_params = $extend_params;
    }
    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->userBill instanceof UserAccountInfo))
        {
            $error = '用户账户信息对象不正确';
            return false;
        }
        if(!isset($this->extend_params['modify_type']) || empty($this->extend_params['modify_type']))
        {
            $error = 'modify_type 不能为空';
            return false;
        }
        $modify_type = $this->extend_params['modify_type'];
        if(!UserAccountInfoUtil::ModifyUserBillAccountInfo($modify_type,$this->extend_params,$this->userBill,$error))
        {
            \Yii::getLogger()->log('修改账户余额失败，'.$error, Logger::LEVEL_ERROR);
            throw new Exception($error);
        }
        if(!isset($outInfo))
        {
            $outInfo = [];
        }
        $outInfo['user_bill'] = $this->userBill;
        return true;
    }
} 