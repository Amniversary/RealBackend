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

class RedPacketStatusSaveByTrans implements ISaveForTransaction
{
    private $redPacketInfo = null;
    private $extend_params = [];

    public function  __construct($rewardInfo, $extend_params=[])
    {
        $this->extend_params = $extend_params;
        $this->redPacketInfo = $rewardInfo;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->redPacketInfo instanceof PersonalRedPackets))
        {
            $error = '非红包录对象';
            return false;
        }
        $status = $this->extend_params['status'];
        if(!in_array(intval($status),[1,2,-1]))//1 已使用  0 未使用 2 使用中, -1 不显示已使用
        {
            $error = '红包状态错误，没有此状态';
            return false;
        }
        $sql = 'update my_personal_red_packets set status=:stus where personal_packets_id=:pid and status <> 1 and status <> -1';
        $rst = \Yii::$app->db->createCommand($sql,[
            ':stus'=>$status,
            ':pid'=>$this->redPacketInfo->personal_packets_id
        ])->execute();
        if($rst <= 0)
        {
            $error = '红包状态错误，红包使用失败';
            return false;
        }
        if(!isset($outInfo))
        {
            $outInfo = [];
        }
        $outInfo['red_packet'] = $this->rewardListInfo;
        return true;
    }
} 