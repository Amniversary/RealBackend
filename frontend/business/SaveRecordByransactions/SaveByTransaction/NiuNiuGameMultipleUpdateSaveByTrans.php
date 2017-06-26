<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/15
 * Time: 0:22
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;

class NiuNiuGameMultipleUpdateSaveByTrans implements ISaveForTransaction
{
    private  $GameMultipleRecord = null;
    private  $extend_params = [];

    public function __construct($record,$extend_params = [])
    {
        $this->GameMultipleRecord = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {

        if($this->extend_params->is_robot == 2)
        {
            $this->GameMultipleRecord['multiple'] = 1;
        }

        $sql = 'update mb_game_seat set multiple = :mt,win_num = :wn,chip_num = :cn,remark1 = :rm WHERE game_id = :gd AND user_id = :id';
        $rst =\Yii::$app->db->createCommand($sql,[
            ':mt'=>$this->GameMultipleRecord['multiple'],
            ':gd'=>$this->GameMultipleRecord['game_id'],
            ':id'=>$this->GameMultipleRecord['user_id'],
            ':wn'=>(($this->GameMultipleRecord['is_int'] == 1) ? 1 : -1),
            ':cn'=>(($this->GameMultipleRecord['is_int'] == 1) ? $this->extend_params->base_num * $this->GameMultipleRecord['multiple'] : $this->extend_params->base_num * $this->GameMultipleRecord['multiple'] * -1),
            ':rm'=>time(),
        ])->execute();

        if($rst <= 0)
        {
            $error = '倍数信息更新失败';
            return false;
        }

        return true;
    }
} 