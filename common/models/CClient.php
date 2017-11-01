<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cClient".
 *
 * @property integer $id
 * @property integer $app_id
 * @property string $nickName
 * @property string $open_id
 * @property string $uuid
 * @property string $avatarUrl
 * @property string $real_name
 * @property string $phone
 * @property string $language
 * @property integer $gender
 * @property string $province
 * @property string $country
 * @property string $user_info
 * @property string $city
 * @property string $skey
 * @property string $session_key
 * @property string $create_time
 * @property string $last_visit_time
 * @property string $remark1
 * @property string $remark2
 */
class CClient extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cClient';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'open_id', 'uuid', 'user_info', 'skey', 'session_key', 'create_time', 'last_visit_time'], 'required'],
            [['app_id', 'gender'], 'integer'],
            [['create_time', 'last_visit_time'], 'safe'],
            [['nickName', 'open_id', 'uuid', 'real_name', 'phone', 'language', 'province', 'country', 'city', 'skey', 'session_key', 'remark1', 'remark2'], 'string', 'max' => 100],
            [['avatarUrl'], 'string', 'max' => 500],
            [['user_info'], 'string', 'max' => 2048],
            [['open_id'], 'unique'],
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
            'nickName' => 'Nick Name',
            'open_id' => 'Open ID',
            'uuid' => 'Uuid',
            'avatarUrl' => 'Avatar Url',
            'real_name' => 'Real Name',
            'phone' => 'Phone',
            'language' => 'Language',
            'gender' => 'Gender',
            'province' => 'Province',
            'country' => 'Country',
            'user_info' => 'User Info',
            'city' => 'City',
            'skey' => 'Skey',
            'session_key' => 'Session Key',
            'create_time' => 'Create Time',
            'last_visit_time' => 'Last Visit Time',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
