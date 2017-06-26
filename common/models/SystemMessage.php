<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_system_message}}".
 *
 * @property integer $message_id
 * @property string $system_message
 * @property string $order
 * @property integer $status
 * @property string $create_time
 * @property string $remark1
 * @property string $remark2
 * @property string $remark3
 * @property string $remark4
 */
class SystemMessage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_system_message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['system_message'], 'string'],
            [['status'], 'integer'],
            [['create_time'], 'safe'],
            [['order'], 'string', 'max' => 10],
            [['remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'message_id' => 'Message ID',
            'system_message' => '消息内容',
            'order' => '排序号',
            'status' => '状态',
            'create_time' => '创建时间',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
            'remark3' => 'Remark3',
            'remark4' => 'Remark4',
        ];
    }
}
