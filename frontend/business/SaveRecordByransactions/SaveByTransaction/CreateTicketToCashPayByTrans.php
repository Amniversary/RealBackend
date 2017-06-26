<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\TicketToCash;
use frontend\business\ClientUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

class CreateTicketToCashPayByTrans implements ISaveForTransaction
{
    private  $ticket_to_cash = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->ticket_to_cash = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->ticket_to_cash instanceof TicketToCash))
        {
            $error = '不是提现记录';
            return false;
        }
        if(!$this->ticket_to_cash->save())
        {
            \Yii::getLogger()->log(var_export($this->ticket_to_cash->getErrors(),true), Logger::LEVEL_ERROR);
            \Yii::getLogger()->log('ticket_to_cash===:'.var_export($this->ticket_to_cash,true), Logger::LEVEL_ERROR);
            throw new Exception('提现记录保存失败');
        }


        if(!isset($outInfo))
        {
            $outInfo = [];
        }
        $outInfo['relate_id'] = $this->ticket_to_cash->record_id;
        return true;
    }
} 