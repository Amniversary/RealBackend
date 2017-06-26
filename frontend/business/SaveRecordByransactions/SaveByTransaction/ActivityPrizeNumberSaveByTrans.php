<?php
/**
 * 修改抽奖礼物数量及用户抽奖次数
 * User: hlq
 * Date: 2016/5/7
 * Time: 10:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

class ActivityPrizeNumberSaveByTrans implements ISaveForTransaction
{
    private  $extend_params=[];

    /**
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($extend_params=[])
    {
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $sql = 'update mb_activity_prize set last_number = last_number-1 where prize_id=:pid and last_number>0';
        $res = \Yii::$app->db->createCommand($sql,[':pid' => $this->extend_params['prize_id']])->execute();
        if($res <= 0)
        {
            $error = '礼物剩余份数更新失败';
            \Yii::getLogger()->log($error.$res = \Yii::$app->db->createCommand($sql,[':pid' => $this->extend_params['prize_id']])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        $sql = 'update mb_activity_chance set number=number-1 where user_id=:uid and number>0';
        $res = \Yii::$app->db->createCommand($sql,[':uid' => $this->extend_params['user_id']])->execute();
        if($res <= 0)
        {
            $error = '用户抽奖次数修改失败';
            \Yii::getLogger()->log($error.$res = \Yii::$app->db->createCommand($sql,[':uid' => $this->extend_params['user_id']])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }
}