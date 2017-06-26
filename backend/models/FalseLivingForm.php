<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/16
 * Time: 10:12
 */

namespace backend\models;


use yii\base\Model;

class FalseLivingForm extends Model
{
    public $living_id;
    public $room_no;
    public $flower_num;
    public $ticket_num;
    public $system_num;
    public $status;
    public $create_time;
    public function rules()
    {
        return [
            [['living_id','flower_num','status','ticket_num','system_num'], 'integer'],
            [['room_no','create_time'], 'safe'],
        ];
    }

    public function  attributeLabels()
    {
        return [
            'living_id' =>'直播ID',
            'flower_num' =>'蜜播ID',
            'ticket_num'=>'直播标题',
            'system_num'=>'用户昵称',
            'status'=>'直播状态',
            'room_no'=>'是否官方',
            'create_time' => '创建时间'
        ];
    }
} 