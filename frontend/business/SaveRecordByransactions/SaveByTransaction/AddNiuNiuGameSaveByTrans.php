<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/13
 * Time: 13:46
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\NiuniuGame;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;

/**
 * 生成牛牛游戏记录 并生成座位信息
 * Class AddNiuNiuGameSaveByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class AddNiuNiuGameSaveByTrans implements ISaveForTransaction
{
    private $NiuniuGameRecord = null;
    private $extend_params = [];

    public function __construct($record, $extend_params= [])
    {
        $this->NiuniuGameRecord = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if (!($this->NiuniuGameRecord instanceof NiuniuGame))
        {
            $error = '不是牛牛游戏记录对象';
            return false;
        }

        if (!$this->NiuniuGameRecord->save())
        {
            $error = '牛牛游戏记录信息保存失败';
            \Yii::getLogger()->log($error.': '.var_export($this->NiuniuGameRecord->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        $sql_params = [];
        $sql = 'insert into mb_game_seat(game_id,user_id,seat_num,seat_status,is_banker,is_robot,is_living_master,poker_info,poker_result,win_num,chip_num,base_num,multiple,is_normal) VALUES';
        $sumMax = count($this->extend_params['poker_info']);
        $i = 1;
        $seat_info = $this->extend_params['seat_info'];
        foreach($this->extend_params['poker_info'] as $info)
        {
            if(!empty($seat_info))
            {
                $sql_params[':gd'.$i] = $this->NiuniuGameRecord->record_id;
                $sql_params[':ud'.$i] = (($i == 1) ? $this->NiuniuGameRecord->living_master_id : null);
                $sql_params[':sn'.$i] = $seat_info[$i-1]['seat_num'];
                $sql_params[':ss'.$i] = 1;
                $sql_params[':ib'.$i] = 1;
                $sql_params[':ir'.$i] = 1;
                $sql_params[':il'.$i] = 1;
                $sql_params[':pi'.$i] = json_encode(['poker1'=>$info[0],'poker2'=>$info[1],'poker3'=>$info[2],'poker4'=>$info[3],'poker5'=>$info[4]]);
                $sql_params[':pr'.$i] = $info['poker_result'];
                $sql_params[':bs'.$i] = (($i == 1) ? 100 : 0);
                $sql_params[':mt'.$i] = (($i == 1) ? 1 : 0);
                if($seat_info[$i-1]['seat_status'] == 2 && $seat_info[$i-1]['is_robot'] != 2 && $seat_info[$i-1]['is_normal'] == 1)
                {
                    $sql_params[':ud'.$i] = $seat_info[$i-1]['user_id'];
                    $sql_params[':sn'.$i] = $seat_info[$i-1]['seat_num'];
                    $sql_params[':ss'.$i] = $seat_info[$i-1]['seat_status'];
                    $sql_params[':il'.$i] = $seat_info[$i-1]['is_living_master'];
                }
                $sql .= sprintf('(:gd%d,:ud%d,:sn%d,:ss%d,:ib%d,:ir%d,:il%d,:pi%d,:pr%d,0,0,:bs%d,:mt%d,1)',$i,$i,$i,$i,$i,$i,$i,$i,$i,$i,$i);
                if($i === $sumMax)
                {
                    $sql .= ';';
                }
                else
                {
                    $sql .= ',';
                }
                $i++;
            }
            else
            {
                $sql_params[':gd'.$i] = $this->NiuniuGameRecord->record_id;
                $sql_params[':ud'.$i] = (($i == 1) ? $this->NiuniuGameRecord->living_master_id : null);
                $sql_params[':sn'.$i] = $i;
                $sql_params[':ss'.$i] = (($i == 1) ? 2 : 1);
                $sql_params[':ib'.$i] = 1;
                $sql_params[':ir'.$i] = 1;
                $sql_params[':il'.$i] = (($i == 1) ? 2 : 1);
                $sql_params[':pi'.$i] = json_encode(['poker1'=>$info[0],'poker2'=>$info[1],'poker3'=>$info[2],'poker4'=>$info[3],'poker5'=>$info[4]]);
                $sql_params[':pr'.$i] = $info['poker_result'];
                $sql_params[':bs'.$i] = (($i == 1) ? 100 : 0);
                $sql_params[':mt'.$i] = (($i == 1) ? 1 : 0);
                $sql .= sprintf('(:gd%d,:ud%d,:sn%d,:ss%d,:ib%d,:ir%d,:il%d,:pi%d,:pr%d,0,0,:bs%d,:mt%d,1)',$i,$i,$i,$i,$i,$i,$i,$i,$i,$i,$i);
                if($i === $sumMax)
                {
                    $sql .= ';';
                }
                else
                {
                    $sql .= ',';
                }
                $i++;
            }
        }

        $rst = \Yii::$app->db->createCommand($sql,$sql_params)->execute();

        if($rst <= 0)
        {
            $error = '座位信息记录初始化失败';
            \Yii::getLogger()->log($error.': '.\Yii::$app->db->createCommand($sql,$sql_params)->rawSql,Logger::LEVEL_ERROR);
            return false;
        }
        //\Yii::getLogger()->log('record_id:'.$this->NiuniuGameRecord->record_id,Logger::LEVEL_ERROR);
        if(empty($this->extend_params['game_name']))
        {
            $up_sql = 'update mb_living set game_name = \'牛牛游戏\' WHERE living_id = :ld';
            $up_rst = \Yii::$app->db->createCommand($up_sql,[
                ':ld'=>$this->NiuniuGameRecord->living_id,
            ])->execute();

            if($up_rst <= 0)
            {
                $error = '更新直播游戏名称失败';
                \Yii::getLogger()->log($error.':  upsql:'.\Yii::$app->db->createCommand($up_sql,[
                        ':ld'=>$this->NiuniuGameRecord->living_id,
                    ])->rawSql,Logger::LEVEL_ERROR);
                return false;
            }
        }

        $outInfo['record_id'] = $this->NiuniuGameRecord->record_id;
        $outInfo['game_status'] = $this->NiuniuGameRecord->game_status;
        return true;
    }
} 