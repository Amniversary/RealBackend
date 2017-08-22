<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_sign_message}}".
 *
 * @property integer $id
 * @property integer $sign_id
 * @property integer $msg_id
 * @property string $remark1
 * @property string $remark2
 */
class SignMessage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_sign_message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sign_id', 'msg_id'], 'integer'],
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
            'msg_id' => 'Msg ID',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
