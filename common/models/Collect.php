<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_collect}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $dynamic_id
 * @property string $remark1
 */
class Collect extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_collect}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'dynamic_id'], 'integer'],
            [['remark1'], 'string', 'max' => 100],
            [['user_id', 'dynamic_id'], 'unique', 'targetAttribute' => ['user_id', 'dynamic_id'], 'message' => 'The combination of User ID and Dynamic ID has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'dynamic_id' => 'Dynamic ID',
            'remark1' => 'Remark1',
        ];
    }
}
