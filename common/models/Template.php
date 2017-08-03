<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_template}}".
 *
 * @property integer $id
 * @property string $template_id
 * @property integer $app_id
 * @property string $title
 * @property string $primary_industry
 * @property string $deputy_industry
 * @property string $content
 * @property string $example
 * @property string $remark1
 * @property string $remark2
 */
class Template extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_template}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id'], 'integer'],
            [['template_id', 'content', 'example'], 'string', 'max' => 300],
            [['title', 'primary_industry', 'deputy_industry', 'remark1', 'remark2'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_id' => '模板 ID',
            'app_id' => '公众号',
            'title' => '模板标题',
            'primary_industry' => '主行业',
            'deputy_industry' => '副行业',
            'content' => '模板内容',
            'example' => '模板例子',
            'remark1' => '备用字段1',
            'remark2' => '备用字段2',
        ];
    }
}
