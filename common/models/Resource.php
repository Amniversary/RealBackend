<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_resource}}".
 *
 * @property integer $id
 * @property integer $app_id
 * @property integer $msg_id
 * @property string $media_id
 * @property integer $update_time
 * @property string $remark1
 * @property string $remark2
 */
class Resource extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_resource}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'msg_id', 'update_time'], 'integer'],
            [['media_id'], 'string', 'max' => 200],
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
            'msg_id' => 'Msg ID',
            'media_id' => 'Media ID',
            'update_time' => 'Update Time',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
