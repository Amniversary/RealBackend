<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_later_params}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $remark1
 * @property string $remark2
 */
class LaterParams extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_later_params}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'remark1', 'remark2'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '配置名称',
            'remark1' => '备用字段1',
            'remark2' => '备用字段2',
        ];
    }
}
