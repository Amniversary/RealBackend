<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_login_info}}".
 *
 * @property integer $rid
 * @property integer $user_id
 * @property string $login_time
 * @property string $record_time
 * @property string $remark1
 * @property string $remark2
 * @property string $remark3
 * @property string $remark4
 */
class LoginInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_login_info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['login_time', 'record_time'], 'safe'],
            [['remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
            [['user_id', 'record_time'], 'unique', 'targetAttribute' => ['user_id', 'record_time'], 'message' => 'The combination of User ID and Record Time has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rid' => 'Rid',
            'user_id' => 'User ID',
            'login_time' => 'Login Time',
            'record_time' => 'Record Time',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
            'remark3' => 'Remark3',
            'remark4' => 'Remark4',
        ];
    }
}
