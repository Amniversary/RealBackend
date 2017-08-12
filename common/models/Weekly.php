<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_weekly}}".
 *
 * @property integer $weekly_id
 * @property string $title
 * @property integer $weeks
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 * @property string $remark1
 * @property string $remark2
 */
class Weekly extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_weekly}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['weeks', 'status'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['title', 'remark1', 'remark2'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'weekly_id' => 'Weekly ID',
            'title' => 'Title',
            'weeks' => 'Weeks',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
