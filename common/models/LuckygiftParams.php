<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_luckygift_params}}".
 *
 * @property integer $lucky_id
 * @property integer $receive_rate
 * @property integer $basic_beans
 * @property integer $multiple
 * @property double $rate
 * @property integer $status
 * @property string $create_time
 * @property string $remark1
 * @property string $remark2
 * @property string $remark3
 * @property string $remark4
 */
class LuckygiftParams extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_luckygift_params}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['receive_rate', 'basic_beans', 'multiple', 'status'], 'integer'],
            [['rate'], 'number','min'=>0,'max'=>100],
            [['receive_rate'], 'number','min'=>0,'max'=>100],
            [['create_time'], 'safe'],
            [['receive_rate', 'basic_beans', 'multiple', 'status'], 'required'],
            [['remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lucky_id' => 'Lucky ID',
            'receive_rate' => '主播收到的票数比例(%)',
            'basic_beans' => '基本豆(触发条件)',
            'multiple' => '倍数',
            'rate' => '概率(%)',
            'status' => '启用',
            'create_time' => '创建时间',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
            'remark3' => 'Remark3',
            'remark4' => 'Remark4',
        ];
    }
}
