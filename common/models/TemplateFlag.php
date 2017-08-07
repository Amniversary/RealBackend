<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_template_flag}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $app_id
 * @property integer $temp_num
 * @property string $remark1
 * @property string $remark2
 */
class TemplateFlag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_template_flag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'app_id', 'temp_num'], 'integer'],
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
            'user_id' => 'User ID',
            'app_id' => 'App ID',
            'temp_num' => 'Temp Num',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
