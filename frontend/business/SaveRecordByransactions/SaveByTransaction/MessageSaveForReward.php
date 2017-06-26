<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:38
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\Message;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

class MessageSaveForReward implements ISaveForTransaction
{
    private  $message = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->message = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->message instanceof Message))
        {
            $error = '不是消息对象';
            return false;
        }
        if(!$this->message->save())
        {
            \Yii::getLogger()->log(var_export($this->message->getErrors(),true), Logger::LEVEL_ERROR);
            throw new Exception('消息保存失败');
        }
        return true;
    }
} 