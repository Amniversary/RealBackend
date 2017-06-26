<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/25
 * Time: 16:18
 */

namespace backend\models;


use yii\base\Model;

class FinanceSonSearchForm extends Model
{
    public $record_id;
    public $client_id;
    public $client_no;
    public $nick_name;
    public $icon_pic;
    public $create_time;
    public $living_time;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id','record_id'], 'integer'],
            [['client_no','nick_name','icon_pic'], 'string'],
            [['create_time'], 'safe'],
        ];
    }

    public function  attributeLabels()
    {
        return [
            'record_id' => '自增ID',
            'client_id'=>'用户表ID',
            'client_no' => '蜜播 ID',
            'nick_name'=>'成员昵称',
            'icon_pic' => '成员头像',
            'create_time' => '成员加入时间',
        ];
    }
} 