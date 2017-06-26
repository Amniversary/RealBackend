<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7
 * Time: 17:01
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\SaveRecordByransactions\ISaveForTransaction;

class ActivityGirlDaySaveByTrans implements ISaveForTransaction
{
    private  $Record = null;
    private  $extend = [];

    public function __construct($record, $extend_params = [])
    {
        $this->Record = $record;
        $this->extend = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(empty($this->Record) && empty($this->extend))
        {
            $error = ' 该时间段榜单数据为空';
            return false;
        }
        if(!empty($this->Record))
        {
            // TODO: 更新女神榜数据
            $ins = 'insert ignore into mb_activity_girl(user_id,value,type) VALUES';
            $flag = count($this->Record);
            $num = 0;
            foreach($this->Record as $getValue)
            {

                $num ++;
                $ins .= sprintf('(%d,0,1)',$getValue['living_master_id']);
                if($num < $flag) {
                    $ins .= ',';
                } else {
                    $ins .= ';';
                }
            }
            \Yii::$app->db->createCommand($ins)->execute();
            $ups = '';
            foreach($this->Record as $getValue)
            {
                $ups .= sprintf('update mb_activity_girl set value = value + %d WHERE user_id = %d AND type = 1;',
                    $getValue['gift_value'],
                    $getValue['living_master_id']);
            }
            $rst = \Yii::$app->db->createCommand($ups)->execute();
            if($rst <= 0)
            {
                $error = '女神榜数据更新失败';
                \Yii::error($error .'  '. \Yii::$app->db->createCommand($ups)->rawSql);
                \Yii::getLogger()->flush(true);
                return false;
            }
        }
        if(!empty($this->extend))
        {
            // TODO: 更新土豪榜数据
            $insert = 'insert ignore into mb_activity_girl(user_id,value,type) VALUES';
            $int = count($this->extend);
            $i = 0;
            foreach($this->extend as $rewardValue)
            {
                $i ++;
                $insert .= sprintf('(%d,0,2)',$rewardValue['reward_user_id']);
                if($i < $int) {
                    $insert .= ',';
                }else{
                    $insert .= ';';
                }
            }
            \Yii::$app->db->createCommand($insert)->execute();
            $update = '';
            foreach($this->extend as $rewardValue)
            {
                $update .= sprintf('update mb_activity_girl set value = value + %d WHERE user_id = %d AND type = 2;',
                    $rewardValue['gift_value'],
                    $rewardValue['reward_user_id']);
            }
            $ret = \Yii::$app->db->createCommand($update)->execute();
            if($ret <= 0)
            {
                $error = '土豪榜数据更新失败';
                \Yii::error($error. ' '.\Yii::$app->db->createCommand($update)->rawSql);
                \Yii::getLogger()->flush(true);
                return false;
            }
        }

        return true;
    }
} 