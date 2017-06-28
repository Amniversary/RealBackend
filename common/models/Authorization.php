<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_authorization}}".
 *
 * @property integer $record_id
 * @property string $app_id
 * @property integer $create_time
 * @property string $verify_ticket
 * @property string $access_token
 * @property string $pre_auth_code
 * @property string $remark1
 * @property string $remark2
 * @property string $remark3
 * @property string $remark4
 */
class Authorization extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_authorization}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time'], 'integer'],
            [['app_id', 'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
            [['verify_ticket', 'access_token', 'pre_auth_code'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'record_id' => 'Record ID',
            'app_id' => 'App ID',
            'create_time' => 'Create Time',
            'verify_ticket' => 'Verify Ticket',
            'access_token' => 'Access Token',
            'pre_auth_code' => 'Pre Auth Code',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
            'remark3' => 'Remark3',
            'remark4' => 'Remark4',
        ];
    }
}
