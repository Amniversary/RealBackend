<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/17
 * Time: 15:12
 */

namespace backend\models;


use yii\base\Model;

class GiftScoreForm extends Model
{
    public $record_id;
    public $gift_id;
    public $gift_name;
    public $pic;
    public $score;

    public function rules()
    {
        return [
            [['gift_id', 'score'], 'integer'],
            [['remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'record_id' => '自增 ID',
            'gift_id' => '礼物 ID',
            'score' => '礼物积分',
            'remark1' => '备用字段1',
            'remark2' => '备用字段2',
            'remark3' => '备用字段3',
            'remark4' => '备用字段4',
        ];
    }

} 