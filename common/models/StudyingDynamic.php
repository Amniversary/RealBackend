<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_studying_dynamic}}".
 *
 * @property integer $dynamic_id
 * @property string $title
 * @property string $pic
 * @property string $content
 * @property integer $type
 * @property integer $comment_count
 * @property integer $count
 * @property integer $create_at
 * @property string $remark1
 * @property string $remark2
 */
class StudyingDynamic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_studying_dynamic}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['type', 'comment_count', 'count', 'create_at'], 'integer'],
            [['title'], 'string', 'max' => 200],
            [['pic'], 'string', 'max' => 400],
            [['remark1', 'remark2'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dynamic_id' => 'Dynamic ID',
            'title' => 'Title',
            'pic' => 'Pic',
            'content' => 'Content',
            'type' => 'Type',
            'comment_count' => 'Comment Count',
            'count' => 'Count',
            'create_at' => 'Create At',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
