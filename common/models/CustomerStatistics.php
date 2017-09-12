<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_customer_statistics}}".
 *
 * @property integer $id
 * @property integer $task_id
 * @property integer $app_id
 * @property integer $user_count
 * @property integer $user_num
 * @property integer $create_time
 * @property string $remark1
 */
class CustomerStatistics extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_customer_statistics}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'app_id', 'user_count', 'user_num', 'create_time'], 'integer'],
            [['remark1'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => '任务 ID',
            'app_id' => '公众号',
            'user_count' => '总用户数',
            'user_num' => '送达人数',
            'create_time' => '结束时间',
            'remark1' => '备用字段1',
        ];
    }
}
