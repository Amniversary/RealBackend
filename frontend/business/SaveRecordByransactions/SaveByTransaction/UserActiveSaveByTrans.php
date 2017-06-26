<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\UserActive;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use frontend\business\UserActiveUtil;
use yii\base\Exception;
use yii\log\Logger;

class UserActiveSaveByTrans implements ISaveForTransaction
{
    private  $userActive = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->userActive = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->userActive instanceof UserActive))
        {
            $error = '非用户活跃度记录对象';
            return false;
        }
        if(empty($this->extend_params['modify_type']))
        {
            $error = '修改活跃度类型不存在';
            return false;
        }
        $modify_type = $this->extend_params['modify_type'];
        unset($this->extend_params['modify_type']);
        if(!UserActiveUtil::ModifyUseractive($modify_type,$this->userActive,$error,$this->extend_params))
        {
            \Yii::getLogger()->log(var_export($this->userActive->getErrors(),true), Logger::LEVEL_ERROR);
            throw new Exception($error);
        }
        if(!isset($outInfo))
        {
            $outInfo = [];
        }
        $outInfo['user_active'] = $this->userActive;
        return true;
    }
} 