<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_statistics_count}}".
 *
 * @property integer $record_id
 * @property integer $app_id
 * @property integer $count_user
 * @property integer $cumulate_user
 * @property string $update_time
 * @property string $remark1
 * @property string $remark2
 */
class StatisticsCount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_statistics_count}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id'], 'required'],
            [['app_id', 'count_user', 'cumulate_user'], 'integer'],
            [['update_time'], 'safe'],
            [['remark1', 'remark2'], 'string', 'max' => 100],
            [['app_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'record_id' => 'Record ID',
            'app_id' => 'App ID',
            'count_user' => 'Count User',
            'cumulate_user' => 'Cumulate User',
            'update_time' => 'Update Time',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
