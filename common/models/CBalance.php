<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cBalance".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $balance
 * @property string $recharge_num
 * @property string $reward_num
 * @property integer $status
 * @property string $rand_str
 * @property string $sign
 * @property string $remark1
 * @property string $remark2
 */
class CBalance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cBalance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'status'], 'integer'],
            [['balance', 'recharge_num', 'reward_num'], 'number'],
            [['rand_str', 'sign', 'remark1', 'remark2'], 'string', 'max' => 100],
            [['user_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'balance' => 'Balance',
            'recharge_num' => 'Recharge Num',
            'reward_num' => 'Reward Num',
            'status' => 'Status',
            'rand_str' => 'Rand Str',
            'sign' => 'Sign',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
