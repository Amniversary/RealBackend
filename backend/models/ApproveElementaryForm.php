<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/19
 * Time: 15:08
 */

namespace backend\models;

use Yii;
use yii\base\Model;

/**
 *  form
 */
class ApproveElementaryForm extends Model
{
    public $approve_id;
    public $register_type;
    public $nick_name;
    public $client_no;
    public $phone_no;
    public $create_time;
    public $status;
    public $check_time;
    public $check_user_name;
    public $device_type;
    public $is_centification;


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
            'approve_id' => '直播认证ID',
            'register_type' => '注册类型',
            'client_no' => '密播id',
            'nick_name' => '真实姓名',
            'phone_no' => '手机号',
            'create_time' => '创建时间',
            'status' => '状态',
            'check_time' => '审核时间',
            'check_user_name' => '审核人名称',
            'check_result_status' => '审核结果',
            'device_type' => '设备类型',
            'is_centification' => '认证',
        ];
    }

}
