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
            'task_id' => 'Task ID',
            'app_id' => 'App ID',
            'user_count' => 'User Count',
            'user_num' => 'User Num',
            'create_time' => 'Create Time',
            'remark1' => 'Remark1',
        ];
    }
}
