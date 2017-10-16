<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cGuest".
 *
 * @property integer $id
 * @property integer $card_id
 * @property integer $user_id
 * @property integer $user_status
 * @property integer $card_status
 * @property string $phone
 * @property string $wish
 * @property integer $num
 * @property string $name
 * @property string $money
 * @property integer $update_time
 * @property string $remark1
 * @property string $remark2
 */
class CGuest extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cGuest';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['card_id', 'user_id'], 'required'],
            [['card_id', 'user_id', 'user_status', 'card_status', 'num', 'update_time'], 'integer'],
            [['money'], 'number'],
            [['phone', 'name', 'remark1', 'remark2'], 'string', 'max' => 100],
            [['wish'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'card_id' => 'Card ID',
            'user_id' => 'User ID',
            'user_status' => 'User Status',
            'card_status' => 'Card Status',
            'phone' => 'Phone',
            'wish' => 'Wish',
            'num' => 'Num',
            'name' => 'Name',
            'money' => 'Money',
            'update_time' => 'Update Time',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
