<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/4
 * Time: 21:02
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\BorrowFund;
use common\models\BusinessCheck;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

class CheckRecordSaveByTrans implements ISaveForTransaction
{
    private  $businessCheck = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->businessCheck = $record;
        $this->extend_params = $extend_params;
    }

    public function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->businessCheck instanceof BusinessCheck))
        {
            $error = '审核记录对象';
            return false;
        }
        if(empty($this->businessCheck->relate_id) &&(
            !isset($outInfo['relate_id']) ||
            empty($outInfo['relate_id'])))
        {
            $error = '相关记录id为空，审核记录异常';
            return false;
        }

        if(empty($this->businessCheck->relate_id))
        {
            $this->businessCheck->relate_id = $outInfo['relate_id'];
        }
        if(!$this->businessCheck->save())
        {
            \Yii::getLogger()->log(var_export($this->businessCheck->getErrors(),true),Logger::LEVEL_ERROR);
            throw new Exception('提现记录保存失败');
        }

        $this->businessCheck->check_no =$this->businessCheck->business_check_id%20;
        if(!$this->businessCheck->save())
        {
            \Yii::getLogger()->log(var_export($this->businessCheck->getErrors(),true),Logger::LEVEL_ERROR);
            throw new Exception('更新审核号失败');
        }

        return true;
    }
} 