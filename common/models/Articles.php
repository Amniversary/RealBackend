<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_articles}}".
 *
 * @property integer $id
 * @property integer $book_id
 * @property string $title
 * @property string $pic
 * @property string $description
 * @property string $url
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 * @property string $remark1
 * @property string $remark2
 */
class Articles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_articles}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['book_id', 'status'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['title', 'description', 'remark1', 'remark2'], 'string', 'max' => 100],
            [['pic', 'url'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'book_id' => 'Book ID',
            'title' => 'Title',
            'pic' => 'Pic',
            'description' => 'Description',
            'url' => 'Url',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
