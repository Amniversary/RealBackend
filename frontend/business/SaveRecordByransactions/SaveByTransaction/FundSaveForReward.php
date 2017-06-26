<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\FundInfo;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;

class FundSaveForReward implements ISaveForTransaction
{
    private  $fundInfo = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->fundInfo = $record;
        $this->extend_params = $extend_params;
    }
    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->fundInfo instanceof FundInfo))
        {
            $error = '不是美愿基金对象';
            return false;
        }
        if(!$this->fundInfo->save())
        {
            \Yii::getLogger()->log(var_export($this->fundInfo->getErrors(),true), Logger::LEVEL_ERROR);
            throw new Exception('美愿基金保存失败');
        }
        return true;
    }
} 