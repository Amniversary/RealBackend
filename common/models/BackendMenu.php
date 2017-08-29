<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_backend_menu}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $backend_id
 * @property string $remark1
 * @property string $remark2
 */
class BackendMenu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_backend_menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'backend_id'], 'integer'],
            [['remark1', 'remark2'], 'string', 'max' => 100],
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
            'backend_id' => 'Backend ID',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
