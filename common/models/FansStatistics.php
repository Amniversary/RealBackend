<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_fans_statistics}}".
 *
 * @property integer $record_id
 * @property integer $app_id
 * @property integer $new_user
 * @property integer $cancel_user
 * @property double $net_user
 * @property integer $total_user
 * @property string $statistics_date
 * @property string $remark1
 * @property string $remark2
 */
class FansStatistics extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_fans_statistics}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'new_user', 'cancel_user', 'total_user'], 'integer'],
            [['net_user'], 'number'],
            [['statistics_date'], 'safe'],
            [['remark1', 'remark2'], 'string', 'max' => 100],
            [['app_id', 'statistics_date'], 'unique', 'targetAttribute' => ['app_id', 'statistics_date'], 'message' => 'The combination of App ID and Statistics Date has already been taken.'],
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
            'new_user' => 'New User',
            'cancel_user' => 'Cancel User',
            'net_user' => 'Net User',
            'total_user' => 'Total User',
            'statistics_date' => 'Statistics Date',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
