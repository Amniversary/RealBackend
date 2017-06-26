<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\Bill;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;

class BillRecordSaveForPayBack implements ISaveForTransaction
{
    private  $billInfo = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->billInfo = $record;
        $this->extend_params = $extend_params;
    }
    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->billInfo instanceof Bill))
        {
            $error = '不是账单记录对象';
            return false;
        }
        if(!isset($this->extend_params['error']) || empty($this->extend_params['error'])) //可能有多个，所有自带错误信息
        {
            $error = '账单信息存储失败后的错误信息不能为空';
            return false;
        }
        $errMsg = $this->extend_params['error'];
        if(!$this->billInfo->save())
        {
            \Yii::getLogger()->log(var_export($this->billInfo->getErrors(),true), Logger::LEVEL_ERROR);
            throw new Exception($errMsg);
        }
        return true;
    }
} 