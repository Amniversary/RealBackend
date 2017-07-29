<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_menu_list}}".
 *
 * @property integer $id
 * @property integer $app_id
 * @property integer $deploy_id
 * @property integer $status
 * @property string $remark1
 * @property string $remark2
 */
class MenuList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_menu_list}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'deploy_id', 'status'], 'integer'],
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
            'app_id' => 'App ID',
            'deploy_id' => 'Deploy ID',
            'status' => 'Status',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
