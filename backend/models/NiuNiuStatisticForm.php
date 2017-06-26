<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/26
 * Time: 14:33
 */

namespace backend\models;


use yii\base\Model;

class NiuNiuStatisticForm extends Model
{
    public $game_id;
    public $chip_player_num;
    public $win_num;
    public $lose_num;
    public $create_time;
    public $nick_name;
    public $client_no;


    public function  attributeLabels()
    {
        return [
            'game_id'=>'牛牛游戏ID',
            'chip_player_num' => '玩家压注总金额',
            'win_num' => '胜场次',
            'lose_num' => '负场次',
            'create_time' => '游戏创建时间',
            'nick_name' => '昵称',
            'client_no' => '蜜播号',
        ];
    }
} 