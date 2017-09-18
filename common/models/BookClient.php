<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_book_client}}".
 *
 * @property integer $client_id
 * @property string $union_id
 * @property string $open_id
 * @property string $nick_name
 * @property integer $sex
 * @property string $pic
 * @property string $country
 * @property string $city
 * @property string $province
 * @property string $remark1
 * @property string $remark2
 */
class BookClient extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_book_client}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sex'], 'integer'],
            [['open_id'] , 'required'],
            [['union_id', 'open_id'], 'string', 'max' => 200],
            [['nick_name', 'country', 'city', 'province', 'remark1', 'remark2'], 'string', 'max' => 100],
            [['pic'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'client_id' => 'Client ID',
            'union_id' => 'Union ID',
            'open_id' => 'Open ID',
            'nick_name' => 'Nick Name',
            'sex' => 'Sex',
            'pic' => 'Pic',
            'country' => 'Country',
            'city' => 'City',
            'province' => 'Province',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
