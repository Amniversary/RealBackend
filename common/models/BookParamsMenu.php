<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_book_params_menu}}".
 *
 * @property integer $id
 * @property integer $book_id
 * @property integer $system_id
 * @property string $remark1
 */
class BookParamsMenu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_book_params_menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['book_id', 'system_id'], 'integer'],
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
            'book_id' => 'Book ID',
            'system_id' => 'System ID',
            'remark1' => 'Remark1',
        ];
    }
}