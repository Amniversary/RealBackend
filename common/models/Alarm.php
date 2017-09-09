<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_alarm}}".
 *
 * @property integer $id
 * @property integer $app_id
 * @property integer $alarm_num
 * @property string $alarm_time
 * @property string $create_time
 * @property string $remark1
 * @property string $remark2
 */
class Alarm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_alarm}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'alarm_num'], 'integer'],
            [['alarm_time', 'create_time'], 'safe'],
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
            'alarm_num' => 'Alarm Num',
            'alarm_time' => 'Alarm Time',
            'create_time' => 'Create Time',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
