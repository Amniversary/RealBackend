<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_batch_customer}}".
 *
 * @property integer $id
 * @property string $task_name
 * @property integer $status
 * @property integer $create_time
 * @property string $app_list
 * @property string $remark1
 * @property string $remark2
 */
class BatchCustomer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_batch_customer}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'create_time'], 'integer'],
            [['task_name', 'remark1', 'remark2'], 'string', 'max' => 100],
            [['app_list'], 'string', 'max' => 1000],
            ['task_name', 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_name' => '任务名称',
            'status' => '任务状态',
            'create_time' => '开始时间',
            'app_list' => '发送列表',
            'remark1' => '备用字段1',
            'remark2' => '备用字段2',
        ];
    }
}
