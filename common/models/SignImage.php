<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_sign_image}}".
 *
 * @property integer $id
 * @property integer $sign_id
 * @property string $pic_url
 * @property string $remark1
 * @property string $remark2
 */
class SignImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_sign_image}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sign_id'], 'integer'],
            [['pic_url'], 'string', 'max' => 500],
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
            'sign_id' => 'Sign ID',
            'pic_url' => '图片信息',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
