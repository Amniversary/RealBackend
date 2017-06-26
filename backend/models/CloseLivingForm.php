<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/26
 * Time: 14:33
 */

namespace backend\models;


use yii\base\Model;

class CloseLivingForm extends Model
{
    public $log_id;
    public $living_id;
    public $living_before_id;
    public $backend_user_id;
    public $backend_user_name;
    public $close_time;
    public $living_master_name;
    public $living_master_no;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    public function  attributeLabels()
    {
        return [
            'log'=>'关闭直播表ID',
            'living_id' => '直播ID',
            'living_before_id' => '直播场次',
            'backend_user_id' => '管理员ID',
            'backend_user_name' => '管理员名称',
            'close_time' => '关闭直播时间',
            'living_master_name' => '主播昵称',
            'living_master_no' => '主播蜜播号',
        ];
    }
} 