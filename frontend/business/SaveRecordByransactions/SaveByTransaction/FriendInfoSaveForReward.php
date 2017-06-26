<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\FriendsList;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;

class FriendInfoSaveForReward implements ISaveForTransaction
{
    private  $friendINfo = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->friendINfo = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->friendINfo instanceof FriendsList))
        {
            $error = '好友记录对象';
            return false;
        }
        if(!$this->friendINfo->save())
        {
            \Yii::getLogger()->log(var_export($this->friendINfo->getErrors(),true), Logger::LEVEL_ERROR);
            throw new Exception('好友记录保存失败');
        }
        if(!isset($outInfo))
        {
            $outInfo = [];
        }
        $outInfo['friend'] = $this->friendINfo;
        return true;
    }
} 