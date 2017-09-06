<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_system_tag}}".
 *
 * @property integer $id
 * @property string $tag_name
 * @property string $remark1
 * @property string $remark2
 */
class SystemTag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_system_tag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag_name', 'remark1', 'remark2'], 'string', 'max' => 100],
            [['tag_name'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tag_name' => '标签名',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
