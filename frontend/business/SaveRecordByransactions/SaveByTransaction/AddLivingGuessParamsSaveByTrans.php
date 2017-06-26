<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 22:05
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;

class AddLivingGuessParamsSaveByTrans implements ISaveForTransaction
{
    private $GuessRecord = null;
    private $extend_params =[];

    public function __construct($record,$extend_params=[])
    {
        $this->GuessRecord = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $insql = 'insert ignore into mb_guess_record(living_id,room_no,user_id,guess_num,free_num,living_type,guess_type,is_ok,guess_money,create_time) VALUE';
        $insql .= sprintf('(%d,\'%s\',%d,0,%d,%d,%d,0,0,\'%s\')',
            $this->GuessRecord['living_id'],
            $this->GuessRecord['room_no'],
            $this->GuessRecord['user_id'],
            $this->GuessRecord['free_num'],
            $this->GuessRecord['living_type'],
            $this->GuessRecord['guess_type'],
            date('Y-m-d H:i:s')
        );

        \Yii::$app->db->createCommand($insql)->execute();

        $upsql = 'update mb_guess_record set guess_num = guess_num + 1,free_num = if(free_num <= 0,0,free_num = free_num - 1),is_ok = :io,guess_money = guess_money + :gm,remark1 = :ct WHERE room_no = :rm AND user_id = :ud AND living_id = :ld AND guess_type = :gt';
        $rst = \Yii::$app->db->createCommand($upsql,[
            ':rm'=>$this->GuessRecord['room_no'],
            ':ud'=>$this->GuessRecord['user_id'],
            ':ld'=>$this->GuessRecord['living_id'],
            ':gt'=>$this->GuessRecord['guess_type'],
            ':io'=>$this->GuessRecord['is_ok'],
            ':gm'=>$this->GuessRecord['guess_money'],
            ':ct'=>date('Y-m-d H:i:s')
        ])->execute();

        if($rst <= 0)
        {
            $error = '更新用户竞猜次数失败';
            \Yii::getLogger()->log($error.' '.\Yii::$app->db->createCommand($upsql,[
                    ':rm'=>$this->GuessRecord['room_no'],
                    ':ud'=>$this->GuessRecord['user_id'],
                    ':ld'=>$this->GuessRecord['living_id'],
                    ':gt'=>$this->GuessRecord['guess_type'],
                    ':io'=>$this->GuessRecord['is_ok'],
                    ':gm'=>$this->GuessRecord['guess_money'],
                    ':ct'=>date('Y-m-d H:i:s')
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }
} 