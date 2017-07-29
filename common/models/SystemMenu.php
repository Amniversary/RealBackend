<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_system_menu}}".
 *
 * @property integer $id
 * @property string $deploy_name
 * @property integer $status
 * @property string $remark1
 * @property string $remark2
 */
class SystemMenu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_system_menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['deploy_name', 'remark1', 'remark2'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'deploy_name' => '配置名称',
            'status' => '状态',
            'remark1' => '备用字段1',
            'remark2' => '备用字段2',
        ];
    }
}
