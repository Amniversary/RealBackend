<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_menu}}".
 *
 * @property integer $menu_id
 * @property string $title
 * @property string $icon
 * @property string $url
 * @property integer $visible
 * @property integer $parent_id
 * @property integer $order_no
 * @property integer $status
 * @property string $remark1
 * @property string $remark2
 * @property string $remark3
 * @property string $remark4
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'icon', 'url', 'visible', 'parent_id', 'status'], 'required'],
            [['visible', 'parent_id', 'order_no', 'status'], 'integer'],
            [['title', 'icon', 'url', 'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'menu_id' => 'Menu ID',
            'title' => 'Title',
            'icon' => 'Icon',
            'url' => 'Url',
            'visible' => 'Visible',
            'parent_id' => 'Parent ID',
            'order_no' => 'Order No',
            'status' => 'Status',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
            'remark3' => 'Remark3',
            'remark4' => 'Remark4',
        ];
    }
}
