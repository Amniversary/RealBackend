<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_sign_keyword}}".
 *
 * @property integer $id
 * @property integer $key_id
 * @property integer $sign_id
 * @property string $remark1
 * @property string $remark2
 */
class SignKeyword extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_sign_keyword}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key_id', 'sign_id'], 'integer'],
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
            'key_id' => 'Key ID',
            'sign_id' => 'Sign ID',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
