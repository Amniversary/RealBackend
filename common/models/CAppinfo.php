<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cAppinfo".
 *
 * @property integer $id
 * @property string $appid
 * @property string $secret
 * @property integer $login_duration
 * @property integer $session_duration
 * @property string $qcloud_appid
 * @property string $ip
 */
class CAppinfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cAppinfo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['appid', 'secret'], 'required'],
            [['login_duration', 'session_duration'], 'integer'],
            [['appid'], 'string', 'max' => 200],
            [['secret', 'qcloud_appid'], 'string', 'max' => 300],
            [['ip'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'appid' => 'Appid',
            'secret' => 'Secret',
            'login_duration' => 'Login Duration',
            'session_duration' => 'Session Duration',
            'qcloud_appid' => 'Qcloud Appid',
            'ip' => 'Ip',
        ];
    }
}
