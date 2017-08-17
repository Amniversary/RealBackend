<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_template_timing}}".
 *
 * @property integer $id
 * @property integer $app_id
 * @property integer $template_id
 * @property string $template_data
 * @property integer $status
 * @property integer $type
 * @property integer $create_time
 * @property string $remark1
 * @property string $remark2
 */
class TemplateTiming extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_template_timing}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'template_id', 'status', 'type', 'create_time'], 'integer'],
            [['template_data'], 'string'],
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
            'template_id' => 'Template ID',
            'template_data' => 'Template Data',
            'status' => 'Status',
            'type' => 'Type',
            'create_time' => 'Create Time',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
