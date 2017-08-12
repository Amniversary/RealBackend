<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_books}}".
 *
 * @property integer $book_id
 * @property integer $weekly_id
 * @property string $title
 * @property string $create_time
 * @property string $update_time
 * @property integer $status
 * @property string $remark1
 * @property string $remark2
 */
class Books extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_books}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['weekly_id', 'status'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['title', 'remark1', 'remark2'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'book_id' => 'Book ID',
            'weekly_id' => 'Weekly ID',
            'title' => 'Title',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'status' => 'Status',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
