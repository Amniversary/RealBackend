<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_system_params}}".
 *
 * @property integer $record_id
 * @property integer $group_id
 * @property string $code
 * @property string $title
 * @property string $description
 * @property string $value1
 * @property string $value2
 * @property string $value3
 * @property string $remark1
 * @property string $remark2
 * @property string $remark3
 * @property string $remark4
 */
class SystemParams extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_system_params}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id'], 'integer'],
            [['code', 'title', 'description', 'value1', 'value2', 'value3', 'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
            [['code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'record_id' => '参数ID',
            'group_id' => '分组ID',
            'code' => '系统参数',
            'title' => '参数标题',
            'description' => '描述',
            'value1' => '参数内容1',
            'value2' => '参数内容2',
            'value3' => '参数内容3',
            'remark1' => '备用字段1',
            'remark2' => '备用字段2',
            'remark3' => '备用字段3',
            'remark4' => '备用字段4',
        ];
    }
}
