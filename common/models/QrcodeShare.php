<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_qrcode_share}}".
 *
 * @property integer $id
 * @property integer $share_user_id
 * @property integer $other_user_id
 * @property integer $app_id
 * @property string $create_time
 * @property string $remark1
 * @property string $remark2
 */
class QrcodeShare extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_qrcode_share}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['share_user_id', 'other_user_id', 'app_id'], 'integer'],
            [['create_time'], 'safe'],
            [['remark1', 'remark2'], 'string', 'max' => 100],
            [['other_user_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'share_user_id' => 'Share User ID',
            'other_user_id' => 'Other User ID',
            'app_id' => 'App ID',
            'create_time' => 'Create Time',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
