<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 15:11
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\GuessRecord;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;

class GuessLivingRecordSaveByTrans implements ISaveForTransaction
{
    private  $GuessRecord = null;
    private  $extend_params=[];


    public function __construct($record,$extend_params=[])
    {
        $this->GuessRecord = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        /****每场直播竞猜统计****/
        $n_insql = 'insert ignore into mb_statistic_guess_num(living_id,room_no,flower_num,ticket_num,system_num) VALUE';
        $n_insql .= sprintf('(%d,%d,0,0,0)',
            $this->GuessRecord['living_id'],
            $this->GuessRecord['room_no']
            );

        \Yii::$app->db->createCommand($n_insql)->execute();

        $n_upsql = 'update mb_statistic_guess_num set flower_num = flower_num + :fn, ticket_num = ticket_num + :tn,system_num = flower_num + ticket_num WHERE living_id = :ld AND room_no = :rm';
        $rst = \Yii::$app->db->createCommand($n_upsql,[
            ':fn'=>$this->GuessRecord['guess_num'],
            ':tn'=>$this->GuessRecord['ticket_num'],
            ':ld'=>$this->GuessRecord['living_id'],
            ':rm'=>$this->GuessRecord['room_no'],
        ])->execute();
        if($rst <= 0)
        {
            $error = '更新每场直播竞猜统计金额失败';
            \Yii::getLogger()->log($error.' '.\Yii::$app->db->createCommand($n_upsql,[
                    ':fn'=>$this->GuessRecord['guess_num'],
                    ':tn'=>$this->GuessRecord['ticket_num'],
                    ':ld'=>$this->GuessRecord['living_id'],
                    ':rm'=>$this->GuessRecord['room_no'],
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        /****更新每日直播竞猜统计****/
        $d_insql = 'insert ignore into mb_statistic_guess(statistic_type,statistic_date,flower_num,ticket_num,system_num) VALUE';
        $d_insql .= sprintf('(1,\'%s\',0,0,0)',
            date('Y-m-d')
            );

        \Yii::$app->db->createCommand($d_insql)->execute();

        $d_upsql = 'update mb_statistic_guess set flower_num = flower_num + :fn,ticket_num = ticket_num + :tn,system_num = flower_num + ticket_num WHERE statistic_type = 1 AND statistic_date = :tim';
        $rst = \Yii::$app->db->createCommand($d_upsql,[
            ':fn'=>$this->GuessRecord['guess_num'],
            ':tn'=>$this->GuessRecord['ticket_num'],
            ':tim'=>date('Y-m-d')
        ])->execute();
        if($rst <= 0)
        {
            $error = '更新每日直播竞猜统计金额失败';
            \Yii::getLogger()->log($error.' '.\Yii::$app->db->createCommand($d_upsql,[
                    ':fn'=>$this->GuessRecord['guess_num'],
                    ':tn'=>$this->GuessRecord['ticket_num'],
                    ':tim'=>date('Y-m-d')
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }


        /****更新每周直播竞猜统计****/
        $w_insql = 'insert ignore into mb_statistic_guess(statistic_type,statistic_date,flower_num,ticket_num,system_num) VALUE ';
        $w_insql .= sprintf('(2,\'%s\',0,0,0)',
            date('Y-W')
        );
        \Yii::$app->db->createCommand($w_insql)->execute();

        $w_upsql = 'update mb_statistic_guess set flower_num = flower_num + :fn,ticket_num = ticket_num + :tn,system_num = flower_num + ticket_num WHERE statistic_type = 2 AND statistic_date = :tim';
        $rst = \Yii::$app->db->createCommand($w_upsql,[
            ':fn'=>$this->GuessRecord['guess_num'],
            ':tn'=>$this->GuessRecord['ticket_num'],
            ':tim'=>date('Y-W')
        ])->execute();

        if($rst <= 0)
        {
            $error = '更新每周直播竞猜统计金额失败';
            \Yii::getLogger()->log($error.' '.\Yii::$app->db->createCommand($w_upsql,[
                    ':fn'=>$this->GuessRecord['guess_num'],
                    ':tn'=>$this->GuessRecord['ticket_num'],
                    ':tim'=>date('Y-W')
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        /****更新每月直播竞猜统计****/
        $m_insql = 'insert ignore into mb_statistic_guess(statistic_type,statistic_date,flower_num,ticket_num,system_num) VALUE ';
        $m_insql .= sprintf('(3,\'%s\',0,0,0)',
            date('Y-m')
            );

        \Yii::$app->db->createCommand($m_insql)->execute();

        $m_upsql = 'update mb_statistic_guess set flower_num = flower_num + :fn,ticket_num = ticket_num + :tn,system_num = flower_num + ticket_num WHERE statistic_type = 3 AND statistic_date = :tim';
        $rst = \Yii::$app->db->createCommand($m_upsql,[
            ':fn'=>$this->GuessRecord['guess_num'],
            ':tn'=>$this->GuessRecord['ticket_num'],
            ':tim'=>date('Y-m')
        ])->execute();

        if($rst <= 0)
        {
            $error = '更新每月直播竞猜统计金额失败';
            \Yii::getLogger()->log($error.' '.\Yii::$app->db->createCommand($m_upsql,[
                    ':fn'=>$this->GuessRecord['guess_num'],
                    ':tn'=>$this->GuessRecord['ticket_num'],
                    ':tim'=>date('Y-m')
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }
} 