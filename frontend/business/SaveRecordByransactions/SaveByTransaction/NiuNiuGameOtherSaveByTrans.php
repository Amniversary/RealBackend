<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;



use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;


/**
 * 牛牛游戏信息
 * Class NiuNiuGameOtherSaveByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class NiuNiuGameOtherSaveByTrans implements ISaveForTransaction
{
    private  $data=[];

    /**
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $sql = 'insert ignore into mb_game_user_record (user_id,game_type,play_num,win_money,lose_money,win_num,lose_num) values(:uid,:gtype,:plnum,:wmoney,:lmoney,:wnum,:lnum)';
        $insert_result = \Yii::$app->db->createCommand($sql,[
            ':uid' => $this->data['user_id'],
            ':gtype' => 1,
            ':plnum' => 0,
            ':wmoney' => 0,
            ':lmoney' => 0,
            ':wnum' => 0,
            ':lnum' => 0,
        ])->execute();

        $sql = 'update mb_game_user_record set play_num=play_num+1,win_money= win_money + :wmoney,lose_money=lose_money + :lmoney,win_num=win_num + :wnum,lose_num=lose_num + :lnum,remark1=:time where user_id=:uid';
        $update_result = \Yii::$app->db->createCommand($sql,[
            ':uid' => $this->data['user_id'],
            ':wmoney' => $this->data['win_money'],
            ':lmoney' => $this->data['lose_money'],
            ':wnum' => ($this->data['win_num'] > $this->data['lose_num'])?1:0,
            ':lnum' => ($this->data['lose_num'] > $this->data['win_num'])?1:0,
            ':time' => time()
        ])->execute();

        if($update_result <= 0)
        {
            \Yii::getLogger()->log('游戏个人记录统计信息写入失败  $sql===:'.\Yii::$app->db->createCommand($sql,[
                    ':uid' => $this->data['user_id'],
                    ':wmoney' => $this->data['win_money'],
                    ':lmoney' => $this->data['lose_money'],
                    ':wnum' => $this->data['win_num'],
                    ':lnum' => $this->data['lose_num'],
                    ':time' => time()
                ])->rawSql,Logger::LEVEL_ERROR);
            $error = '游戏个人记录统计信息写入失败 sql===:'.\Yii::$app->db->createCommand($sql,[
                    ':uid' => $this->data['user_id'],
                    ':wmoney' => $this->data['win_money'],
                    ':lmoney' => $this->data['lose_money'],
                    ':wnum' => $this->data['win_num'],
                    ':lnum' => $this->data['lose_num'],
                    ':time' => time()
                ])->rawSql;
            return false;
        }

        /***************游戏押注金额******************/
        $sql = 'insert ignore into mb_game_chip_money (game_id,chip_player_num,chip_num) values(:gid,:cpnum,:cnum)';
        $insert_result = \Yii::$app->db->createCommand($sql,[
            ':gid' => $this->data['game_id'],
            ':cpnum' => 0,
            ':cnum' => 0,
        ])->execute();

        $sql = 'update mb_game_chip_money set chip_player_num=chip_player_num+:cpnum,chip_num=chip_num+:cnum,remark1=:rem1 where game_id=:gid';
        $update_result = \Yii::$app->db->createCommand($sql,[
            ':gid' => $this->data['game_id'],
            ':cpnum' => $this->data['chip_player_num'],
            ':cnum' => $this->data['chip_num'],
            ':rem1' => time()
        ])->execute();

        if($update_result <= 0)
        {
            \Yii::getLogger()->log('游戏押注金额信息写入失败  $sql===:'.\Yii::$app->db->createCommand($sql,[
                    ':gid' => $this->data['game_id'],
                    ':cpnum' => 0,
                    ':cnum' => 0,
                ])->rawSql,Logger::LEVEL_ERROR);
            $error = '游戏押注金额信息写入失败 sql===:'.\Yii::$app->db->createCommand($sql,[
                    ':gid' => $this->data['game_id'],
                    ':cpnum' => 0,
                    ':cnum' => 0,
                ])->rawSql;
            return false;
        }

        /************游戏记录胜负金额信息**************/
        $sql = 'insert ignore into mb_game_result_money (game_id,win_chip_num) values(:gid,:wcnum)';
        $insert_result = \Yii::$app->db->createCommand($sql,[
            ':gid' => $this->data['game_id'],
            ':wcnum' => 0,
        ])->execute();

        $sql = 'update mb_game_result_money set win_chip_num=win_chip_num+:wcnum,remark1=:time where game_id=:gid';
        $update_result = \Yii::$app->db->createCommand($sql,[
            ':gid' => $this->data['game_id'],
            ':wcnum' => $this->data['win_chip_num'],
            ':time' => time()
        ])->execute();

        if($update_result <= 0)
        {
            \Yii::getLogger()->log('游戏记录胜负金额信息写入失败  $sql===:'.\Yii::$app->db->createCommand($sql,[
                    ':gid' => $this->data['game_id'],
                    ':wcnum' => $this->data['win_chip_num'],
                ])->rawSql,Logger::LEVEL_ERROR);
            $error = '游戏记录胜负金额信息写入失败  sql==:'.\Yii::$app->db->createCommand($sql,[
                    ':gid' => $this->data['game_id'],
                    ':wcnum' => $this->data['win_chip_num'],
                ])->rawSql;
            return false;
        }

        /************游戏记录日统计**************/
        $sql = 'insert ignore into mb_game_statistic (game_type,statistic_type,statistic_time,statistic_num) values(:gtype,:wcnum,:stime,:snum)';
        $insert_result = \Yii::$app->db->createCommand($sql,[
            ':gtype' => 1,
            ':wcnum' => 1,
            ':stime' => date('Y-m-d'),
            ':snum' => 0
        ])->execute();

        $sql = 'update mb_game_statistic set statistic_num=statistic_num+:snum,remark1=:time where game_type=:gtype and statistic_type=:wcnum and statistic_time=:stime';
        $update_result = \Yii::$app->db->createCommand($sql,[
            ':gtype' => 1,
            ':wcnum' => 1,
            ':stime' => date('Y-m-d'),
            ':snum' => $this->data['statistic_num'],
            ':time' => time()
        ])->execute();

        if($update_result <= 0)
        {
            \Yii::getLogger()->log('游戏记录日统计信息写入失败  $sql===:'.\Yii::$app->db->createCommand($sql,[
                    ':gtype' => 1,
                    ':wcnum' => 1,
                    ':stime' => date('Y-m-d'),
                    ':snum' => $this->data['statistic_num']
                ])->rawSql,Logger::LEVEL_ERROR);
            $error = '游戏记录日统计信息写入失败';
            return false;
        }

        /************游戏记录周统计**************/
        $sql = 'insert ignore into mb_game_statistic (game_type,statistic_type,statistic_time,statistic_num) values(:gtype,:wcnum,:stime,:snum)';
        $insert_result = \Yii::$app->db->createCommand($sql,[
            ':gtype' => 1,
            ':wcnum' => 2,
            ':stime' => date('Y-W'),
            ':snum' => 0
        ])->execute();

        $sql = 'update mb_game_statistic set statistic_num=statistic_num+:snum,remark1=:time where game_type=:gtype and statistic_type=:wcnum and statistic_time=:stime';
        $update_result = \Yii::$app->db->createCommand($sql,[
            ':gtype' => 1,
            ':wcnum' => 2,
            ':stime' => date('Y-W'),
            ':snum' => $this->data['statistic_num'],
            ':time' => time()
        ])->execute();

        if($update_result <= 0)
        {
            \Yii::getLogger()->log('游戏记录周统计信息写入失败  $sql===:'.\Yii::$app->db->createCommand($sql,[
                    ':gtype' => 1,
                    ':wcnum' => 2,
                    ':stime' => date('Y-W'),
                    ':snum' => $this->data['statistic_num']
                ])->rawSql,Logger::LEVEL_ERROR);
            $error = '游戏记录周统计信息写入失败';
            return false;
        }

        /************游戏记录月统计**************/
        $sql = 'insert ignore into mb_game_statistic (game_type,statistic_type,statistic_time,statistic_num) values(:gtype,:wcnum,:stime,:snum)';
        $insert_result = \Yii::$app->db->createCommand($sql,[
            ':gtype' => 1,
            ':wcnum' => 3,
            ':stime' => date('Y-m'),
            ':snum' => 0
        ])->execute();

        $sql = 'update mb_game_statistic set statistic_num=statistic_num+:snum,remark1=:time where game_type=:gtype and statistic_type=:wcnum and statistic_time=:stime';
        $update_result = \Yii::$app->db->createCommand($sql,[
            ':gtype' => 1,
            ':wcnum' => 3,
            ':stime' => date('Y-m'),
            ':snum' => $this->data['statistic_num'],
            ':time' => time()
        ])->execute();

        if($update_result <= 0)
        {
            \Yii::getLogger()->log('游戏记录月统计信息写入失败  $sql===:'.\Yii::$app->db->createCommand($sql,[
                    ':gtype' => 1,
                    ':wcnum' => 2,
                    ':stime' => date('Y-m'),
                    ':snum' => $this->data['statistic_num']
                ])->rawSql,Logger::LEVEL_ERROR);
            $error = '游戏记录月统计信息写入失败';
            return false;
        }
        return true;
    }

}