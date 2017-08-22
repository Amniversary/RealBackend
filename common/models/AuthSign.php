<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_auth_sign}}".
 *
 * @property integer $id
 * @property integer $app_id
 * @property integer $user_id
 * @property integer $sign_num
 * @property string $update_time
 * @property string $create_time
 * @property string $remark1
 * @property string $remark2
 */
class AuthSign extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_auth_sign}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'user_id', 'sign_num'], 'integer'],
            [['update_time', 'create_time'], 'safe'],
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
            'app_id' => 'App ID',
            'user_id' => 'User ID',
            'sign_num' => 'Sign Num',
            'update_time' => 'Update Time',
            'create_time' => 'Create Time',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
