<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_later_keyword}}".
 *
 * @property integer $id
 * @property integer $later_id
 * @property integer $key_id
 * @property string $remark1
 */
class LaterKeyword extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_later_keyword}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['later_id', 'key_id'], 'integer'],
            [['remark1'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'later_id' => 'Later ID',
            'key_id' => 'Key ID',
            'remark1' => 'Remark1',
        ];
    }
}
