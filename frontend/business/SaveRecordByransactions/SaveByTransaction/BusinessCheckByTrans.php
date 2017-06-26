<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\BusinessCheck;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;

class BusinessCheckByTrans implements ISaveForTransaction
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

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->businessCheck instanceof BusinessCheck))
        {
            $error = '不是审核记录';
            return false;
        }
        if(!$this->businessCheck->save())
        {
            \Yii::getLogger()->log(var_export($this->businessCheck->getErrors(),true), Logger::LEVEL_ERROR);
            throw new Exception('审核记录保存失败');
        }
        if(!isset($outInfo))
        {
            $outInfo = [];
        }
        $outInfo['business_check'] = $this->businessCheck;
        return true;
    }
} 