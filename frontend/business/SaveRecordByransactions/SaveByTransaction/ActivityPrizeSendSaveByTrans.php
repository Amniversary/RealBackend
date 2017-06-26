<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/23
 * Time: 21:39
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\LuckyDrawRecord;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;

class ActivityPrizeSendSaveByTrans implements ISaveForTransaction
{
    private $PrizeInfoRecord = null;
    private $extend_params =[];

    public function __construct($record,$extend_params=[])
    {
        $this->PrizeInfoRecord = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->PrizeInfoRecord instanceof LuckyDrawRecord))
        {
            $error = '不是抽奖记录对象';
            return false;
        }

        if(!$this->PrizeInfoRecord->save())
        {
            $error = '抽奖记录对象保存失败';
            \Yii::getLogger()->log($error. ' :'.var_export($this->PrizeInfoRecord->getErrors(),true),Logger::LEVEL_ERROR);
            \Yii::getLogger()->flush(true);
            return false;
        }


        return true;
    }
} 