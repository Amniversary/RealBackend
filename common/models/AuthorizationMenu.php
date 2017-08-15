<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_authorization_menu}}".
 *
 * @property integer $menu_id
 * @property integer $app_id
 * @property string $name
 * @property string $type
 * @property string $key_type
 * @property integer $is_list
 * @property integer $parent_id
 * @property string $url
 * @property integer $global
 * @property string $remark1
 * @property string $remark2
 * @property string $remark3
 * @property string $remark4
 */
class AuthorizationMenu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_authorization_menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'],'required'],
            [['app_id','is_list','global'], 'integer'],
            [['url'], 'string', 'max' => 200],
            [['name', 'type', 'key_type', 'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'menu_id' => '菜单 ID',
            'app_id' => '公众号名称',
            'name' => '菜单名称',
            'type' => '菜单事件',
            'key_type' => '事件标签',
            'url'=> '跳转链接',
            'is_list' => '是否有二级菜单',
            'parent_id' => '父级菜单id',
            'global'=>'全局配置',
            'remark1' => '备用字段1',
            'remark2' => '备用字段2',
            'remark3' => '备用字段3',
            'remark4' => '备用字段4',
        ];
    }
}
