<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_keyword_params}}".
 *
 * @property integer $id
 * @property integer $app_id
 * @property integer $key_id
 * @property integer $msg_id
 * @property string $remark1
 * @property string $remark2
 */
class KeywordParams extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_keyword_params}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'key_id', 'msg_id'], 'integer'],
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
            'key_id' => 'Key ID',
            'msg_id' => 'Msg ID',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
