<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_batch_customer_params}}".
 *
 * @property integer $id
 * @property integer $task_id
 * @property integer $msg_id
 * @property string $remark1
 */
class BatchCustomerParams extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_batch_customer_params}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'msg_id'], 'integer'],
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
            'msg_id' => 'Msg ID',
            'remark1' => 'Remark1',
        ];
    }
}
