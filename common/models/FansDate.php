<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_fans_date}}".
 *
 * @property integer $id
 * @property integer $app_id
 * @property integer $new_user
 * @property integer $cancel_user
 * @property integer $net_user
 * @property integer $statistics_date
 * @property string $create_time
 * @property string $remark1
 */
class FansDate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_fans_date}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'new_user', 'cancel_user', 'net_user'], 'integer'],
            [['create_time'], 'safe'],
            [['statistics_date'], 'string', 'max' => 50],
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
            'app_id' => 'App ID',
            'new_user' => 'New User',
            'cancel_user' => 'Cancel User',
            'net_user' => 'Net User',
            'statistics_date' => 'Statistics Date',
            'create_time' => 'Create Time',
            'remark1' => 'Remark1',
        ];
    }
}
