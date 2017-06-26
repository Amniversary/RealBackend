<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\Report;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

class ReportInfoSaveByTrans implements ISaveForTransaction
{
    private  $reportInfo = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->reportInfo = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->reportInfo instanceof Report))
        {
            $error = '不是举报记录';
            return false;
        }
        if(!$this->reportInfo->save())
        {
            \Yii::getLogger()->log(var_export($this->reportInfo->getErrors(),true), Logger::LEVEL_ERROR);
            throw new Exception('举报记录保存失败');
        }
        return true;
    }
} 