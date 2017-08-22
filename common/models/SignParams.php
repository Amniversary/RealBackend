<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_sign_params}}".
 *
 * @property integer $id
 * @property integer $app_id
 * @property integer $day_id
 * @property integer $type
 * @property string $remark1
 * @property string $remark2
 */
class SignParams extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_sign_params}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'day_id', 'type'], 'integer'],
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
            'app_id' => '公众号',
            'day_id' => '日期',
            'type' => '类型',
            'remark1' => '备用字段1',
            'remark2' => '备用字段2',
        ];
    }

    public function getParamsDayName($status)
    {
        $ary = ['周日','周一','周二','周三','周四','周五','周六'];
        return $ary[$status];
    }



}
