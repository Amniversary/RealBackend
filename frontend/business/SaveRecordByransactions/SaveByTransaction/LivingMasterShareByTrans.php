<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 16:54
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;

class LivingMasterShareByTrans implements ISaveForTransaction
{
    private $getLivingShareRecord = null;
    private $extend_params = [];

    public function __construct($record,$extend_params=[])
    {
        $this->getLivingShareRecord = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $joinSql = 'insert ignore into mb_statistic_livingmaster_share (share_date,living_master_id,living_master_share_no,audience_share_no,total_no) values(:tm,:lmd,0,0,0)';

        $query = \Yii::$app->db->createCommand($joinSql,[
            ':tm'=>$this->getLivingShareRecord['time'],
            ':lmd'=>$this->getLivingShareRecord['living_master_id'],
        ])->execute();

        if($this->getLivingShareRecord['client_type'] == 1)
        {
            $upSql = 'update mb_statistic_livingmaster_share set living_master_share_no = living_master_share_no + 1,total_no = (living_master_share_no + audience_share_no) WHERE living_master_id = :md AND share_date = :dt';
            $upQuery = \Yii::$app->db->createCommand($upSql,[
                ':md'=>$this->getLivingShareRecord['living_master_id'],
                ':dt'=>$this->getLivingShareRecord['time'],
            ])->execute();

            if($upQuery <= 0)
            {
                $error = '更新主播分享信息统计失败';
                \Yii::getLogger()->log($error.' : living_master_id: '.$this->getLivingShareRecord['living_master_id'].\Yii::$app->db->createCommand($upQuery,[
                        ':md' => $this->getLivingShareRecord['living_master_id'],
                        ':dt'=>$this->getLivingShareRecord['time']])->rawSql,
                    Logger::LEVEL_ERROR);
                return false;
            }
        }
        else
        {
            $upSql = 'update mb_statistic_livingmaster_share set audience_share_no = audience_share_no + 1,total_no = (living_master_share_no + audience_share_no) WHERE living_master_id = :mdl AND share_date = :tt';
            $upQuery = \Yii::$app->db->createCommand($upSql,[
                ':mdl'=>$this->getLivingShareRecord['living_master_id'],
                ':tt'=>$this->getLivingShareRecord['time'],
            ])->execute();

            if($upQuery <= 0)
            {
                $error = '更新观众分享信息统计失败';
                \Yii::getLogger()->log($error.' : living_master_id:'.$this->getLivingShareRecord['living_master_id'].\Yii::$app->db->createCommand($upSql,[
                        ':mdl'=>$this->getLivingShareRecord['living_master_id'],
                        ':tt'=>$this->getLivingShareRecord['time']])->rawSql,
                    Logger::LEVEL_ERROR);
                return false;
            }
        }

        return true;
    }
} 