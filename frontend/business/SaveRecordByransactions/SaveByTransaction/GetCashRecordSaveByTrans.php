<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\GetCash;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

class GetCashRecordSaveByTrans implements ISaveForTransaction
{
    private  $getCashRecord = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->getCashRecord = $record;
        $this->extend_params = $extend_params;
    }
    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->getCashRecord instanceof GetCash))
        {
            $error = '不是余额提现记录对象';
            return false;
        }
        if(!$this->getCashRecord->save())
        {
            \Yii::getLogger()->log(var_export($this->getCashRecord->getErrors(),true), Logger::LEVEL_ERROR);
            throw new Exception('余额提现记录存储失败');
        }
        if(!isset($outInfo))
        {
            $outInfo = [];
        }
        $outInfo['get_cash'] = $this->getCashRecord;
        return true;
    }
} 