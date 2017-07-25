<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_qrcode_img}}".
 *
 * @property integer $record_id
 * @property integer $client_id
 * @property string $media_id
 * @property integer $update_time
 * @property string $remark1
 * @property string $remark2
 */
class QrcodeImg extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_qrcode_img}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'update_time'], 'integer'],
            [['media_id'], 'string', 'max' => 200],
            [['remark1', 'remark2'], 'string', 'max' => 100],
            [['client_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'record_id' => 'Record ID',
            'client_id' => 'Client ID',
            'media_id' => 'Media ID',
            'pic_url' => 'Pic Url',
            'update_time' => 'Update Time',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
