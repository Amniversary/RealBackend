<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_voice}}".
 *
 * @property integer $id
 * @property integer $dynamic_id
 * @property string $voice
 * @property string $remark1
 * @property string $remark2
 */
class Voice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_voice}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dynamic_id'], 'integer'],
            [['voice'], 'string', 'max' => 300],
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
            'dynamic_id' => 'Dynamic ID',
            'voice' => 'Voice',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
