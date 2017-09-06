<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_system_tag_menu}}".
 *
 * @property integer $id
 * @property integer $tag_id
 * @property integer $auth_id
 * @property string $remark1
 */
class SystemTagMenu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_system_tag_menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag_id', 'auth_id'], 'integer'],
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
            'tag_id' => 'Tag ID',
            'auth_id' => 'Auth ID',
            'remark1' => 'Remark1',
        ];
    }
}
