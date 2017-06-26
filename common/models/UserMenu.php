<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_user_menu}}".
 *
 * @property integer $record_id
 * @property integer $user_id
 * @property integer $menu_id
 * @property string $remark1
 * @property string $remark2
 * @property string $remark3
 * @property string $remark4
 */
class UserMenu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_user_menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'menu_id'], 'required'],
            [['user_id', 'menu_id'], 'integer'],
            [['remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'record_id' => 'Record ID',
            'user_id' => 'User ID',
            'menu_id' => 'Menu ID',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
            'remark3' => 'Remark3',
            'remark4' => 'Remark4',
        ];
    }
}
