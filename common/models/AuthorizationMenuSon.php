<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_authorization_menu_son}}".
 *
 * @property integer $record_id
 * @property integer $menu_id
 * @property string $name
 * @property string $type
 * @property string $url
 * @property string $key_type
 * @property string $remark1
 * @property string $remark2
 */
class AuthorizationMenuSon extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_authorization_menu_son}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['menu_id'], 'integer'],
            [['name', 'type', 'key_type', 'remark1', 'remark2'], 'string', 'max' => 100],
            [['url'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'record_id' => 'Record ID',
            'menu_id' => '一级菜单 ID',
            'name' => '二级菜单名称',
            'type' => '菜单事件',
            'url' => '跳转链接',
            'key_type' => '事件标签',
            'remark1' => '备用字段1',
            'remark2' => '备用字段2',
        ];
    }
}
