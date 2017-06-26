<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 *  form
 */
class ApproveBusinessCheckForm extends Model
{
    public $approve_id;
    public $actual_name;
    public $bank_account;
    public $phone_num;
    public $id_card;
    public $create_time;
    public $status;
    public $id_card_pic_all;
    public $id_card_pic_main;
    public $id_card_pic_turn;
    public $create_user_name;
    public $check_no;
    public $refused_reason;
    public $check_time;
    public $check_user_name;
    public $check_result_status;
    public $create_user_id;
    public $check_user_id;
    public $client_no;
    public $family_name;
    public $wechat;
    public $qq;
    public $account_name;
    public $bank;
    public $address;


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
            'check_user_id' => '审核人id',
            'create_user_id' => '用户id',
            'actual_name' => '真实姓名',
            'bank_account' => '银行账号',
            'phone_num' => '手机号',
            'id_card' => '身份证号',
            'create_time' => '创建时间',
            'status' => '状态',
            'id_card_pic_all' => '手持身份证照',
            'id_card_pic_main' => '身份证正面照',
            'id_card_pic_turn' => '身份证反面照',
            'create_user_name' => '用户昵称',
            'check_no' => '审核分配号',
            'refused_reason' => '拒绝原因',
            'check_time' => '审核时间',
            'check_user_name' => '审核人名称',
            'check_result_status' => '审核结果',
            'client_no' => '蜜播ID',
            'family_name' => '家族名称',
            'wechat' => '微信号',
            'qq' => 'QQ号',
            'account_name' => '开户名',
            'bank' => '开户行',
            'address' => '地址',
        ];
    }

}
