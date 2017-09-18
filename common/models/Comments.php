<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_comments}}".
 *
 * @property integer $id
 * @property integer $dynamic_id
 * @property integer $user_id
 * @property integer $parent_id
 * @property string $content
 * @property integer $status
 * @property integer $create_at
 * @property string $remark1
 * @property string $remark2
 */
class Comments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_comments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dynamic_id', 'user_id', 'parent_id', 'status', 'create_at'], 'integer'],
            [['content'], 'string'],
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
            'user_id' => 'User ID',
            'parent_id' => 'Parent ID',
            'content' => 'Content',
            'status' => 'Status',
            'create_at' => 'Create At',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
