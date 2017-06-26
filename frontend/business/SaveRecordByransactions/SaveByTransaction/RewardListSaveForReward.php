<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:52
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\RewardList;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

class RewardListSaveForReward implements ISaveForTransaction
{
    private $rewardListInfo = null;
    private $extend_params = [];

    public function  __construct($rewardInfo, $extend_params=[])
    {
        $this->extend_params = $extend_params;
        $this->rewardListInfo = $rewardInfo;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->rewardListInfo instanceof RewardList))
        {
            $error = '非打赏记录对象';
            return false;
        }
        if(!$this->rewardListInfo->save())
        {
            \Yii::getLogger()->log(var_export($this->rewardListInfo->getErrors(),true), Logger::LEVEL_ERROR);
            throw new Exception('打赏记录存储失败');
        }
        if(!isset($outInfo))
        {
            $outInfo = [];
        }
        $outInfo['reward_list'] = $this->rewardListInfo;
        return true;
    }
} 