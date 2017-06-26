<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 10:49
 */
namespace backend\models;

use Yii;
use yii\base\Model;

class SignActhor extends Model{

    public $salary_id;
    public $user_id;
    public $anchor_salary;
    public $anchor_time;
    public $is_del;
    public $client_id;
    public $client_no;
    public $nick_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['anchor_time'], 'safe'],
            [['salary_id', 'user_id','anchor_salary','is_del','client_id','client_no'], 'integer'],
            [['nick_name'], 'string', 'max' => 100],
        ];
    }

    public function  attributeLabels()
    {
        return [
            'salary_id'=>'薪资表ID',
            'user_id' => '签约主播ID',
            'anchor_salary'=>'薪资',
            'anchor_time'=>'签约时间',
            'is_del'=>'删除',
            'client_id' => '主播ID',
            'client_no' => '蜜播ID',
            'nick_name' => '蜜播昵称',
        ];
    }
}
