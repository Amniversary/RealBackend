<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_set_user_check_no}}".
 *
 * @property integer $set_check_no_id
 * @property integer $user_id
 * @property integer $start_no
 * @property integer $end_no
 * @property string $remark1
 * @property string $remark2
 * @property string $remark3
 * @property string $remark4
 */
class SetUserCheckNo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_set_user_check_no}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'start_no', 'end_no'], 'integer'],
            [['remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
            [['user_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'set_check_no_id' => 'Set Check No ID',
            'user_id' => 'User ID',
            'start_no' => '开始号',
            'end_no' => '结束号',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
            'remark3' => 'Remark3',
            'remark4' => 'Remark4',
        ];
    }
}
