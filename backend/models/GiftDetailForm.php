<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 *  form
 */
class GiftDetailForm extends Model
{
    public $device_type;
    public $gift_name;
    public $gift_value;
    public $living_master_id;
    public $client_no;
    public $nick_name;
    public $before_balance;
    public $after_balance;
    public $create_time;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    public function  attributeLabels()
    {
        return [
            'device_type' => '设备类型',
            'gift_name' => '礼物名称',
            'gift_value' => '礼物豆数',
            'living_master_id' => '收礼物者client_id',
            'client_no' => '收礼物者蜜播ID',
            'nick_name' => '收礼物者昵称',
            'before_balance' => '操作前金额',
            'after_balance' => '操作后金额',
            'create_time' => '操作时间',
        ];
    }

}
