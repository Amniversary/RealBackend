<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_lucky_draw_record}}".
 *
 * @property integer $record_id
 * @property integer $activity_id
 * @property integer $user_id
 * @property integer $device_type
 * @property string $reward_name
 * @property string $prize_name
 * @property integer $prize_type
 * @property integer $prize_num
 * @property string $num_unit
 * @property integer $is_winning
 * @property integer $is_send
 * @property string $prize_user_name
 * @property string $prize_user_site
 * @property string $create_time
 * @property string $express_num
 * @property integer $is_direct_send
 * @property string $remark1
 * @property string $remark2
 * @property string $remark3
 * @property string $remark4
 */
class LuckyDrawRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_lucky_draw_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_id', 'user_id', 'device_type', 'prize_type', 'prize_num', 'is_winning', 'is_send', 'is_direct_send'], 'integer'],
            [['create_time'], 'safe'],
            [['reward_name', 'prize_name', 'prize_user_name', 'express_num', 'remark1', 'remark2', 'remark3', 'remark4', 'num_unit'], 'string', 'max' => 100],
            [['prize_user_site'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'record_id' => '记录 ID',
            'activity_id' => '活动 ID',
            'user_id' => '用户 ID',
            'device_type' => '设备类型',
            'reward_name' => '奖励名称',
            'prize_name' => '奖品名称',
            'prize_type' => '奖品类型',
            'prize_num' => '奖品数量',
            'num_unit' => '奖品单位',
            'is_winning' => '是否中奖',
            'is_send' => '是否发送',
            'prize_user_name' => '收奖人姓名',
            'prize_user_site' => '收奖人地址',
            'create_time' => '中奖时间',
            'express_num' => '快递单号',
            'is_direct_send' => '是否直接分发',
            'remark1' => '备用字段1',
            'remark2' => '备用字段2',
            'remark3' => '备用字段3',
            'remark4' => '备用字段4',
        ];
    }
}
