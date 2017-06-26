<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/13
 * Time: 15:50
 */
namespace backend\models;

use Yii;
use yii\base\Model;

class Quiz extends Model{

    public $record_id;
    public $living_id;
    public $room_no;
    public $living_master_id;
    public $user_id;
    public $is_ok;
    public $living_type;
    public $guess_type;
    public $guess_money;
    public $create_time;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time'], 'safe'],
            [['record_id', 'living_id','room_no','living_master_id','user_id','is_ok','living_type','guess_type','guess_money'], 'integer']
        ];
    }

    public function  attributeLabels()
    {
        return [
            'record_id'=>'自增id',
            'living_id' => '直播间id',
            'room_no'=>'房间编号',
            'living_master_id'=>'主播蜜播id',
            'user_id'=>'竞猜人id',
            'is_ok' => '是否竞猜成功',
            'living_type' => '直播类型',
            'guess_type' => '竞猜类型',
            'guess_money' => '竞猜金额',
            'create_time' => '创建时间',
        ];
    }
}
