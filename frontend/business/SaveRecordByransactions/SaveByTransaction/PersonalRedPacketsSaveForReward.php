<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:52
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\PersonalRedPackets;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

class PersonalRedPacketsSaveForReward implements ISaveForTransaction
{
    private  $personalRedPackets = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->personalRedPackets = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->personalRedPackets instanceof PersonalRedPackets))
        {
            $error = '非个人红包记录对象';
            return false;
        }
        if(!$this->personalRedPackets->save())
        {
            \Yii::getLogger()->log(var_export($this->personalRedPackets->getErrors(),true), Logger::LEVEL_ERROR);
            throw new Exception('修改个人红包记录失败');
        }
        if(!isset($outInfo))
        {
            $outInfo = [];
        }
        $outInfo['personal_red_packet'] = $this->personalRedPackets;
        return true;
    }
} 