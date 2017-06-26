<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class TickToCashForm extends Model
{
//['u.nick_name','ticket_num','real_cash_money', 'cash_type','t.status','cash_rate','refuesd_reason','finance_remark','t.create_time','check_time','finace_ok_time']
    public $user_id;
    public $record_id;
    public $nick_name;
    public $ticket_num;
    public $real_cash_money;
    public $cash_type;
    public $status;
    public $cash_rate;
    public $refuesd_reason;
    public $finance_remark;
    public $create_time;
    public $check_time;
    public $finace_ok_time;
    public $client_id;
    public $client_no;
    public $alipay_no;
    public $real_name;
    public $fail_status;


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
            'user_id'=>'用户id',
            'record_id' => '审核记录id',
            'cash_type' => '提现方式',
            'ticket_num' => '提现票数',
            'cash_rate' => '提现费率',
            'cash_fees' => 'Cash Fees',
            'real_cash_money' => '除手续费后的提现金额',
            'status' => '状态',
            'refuesd_reason' => '拒绝原因',
            'finance_remark' => '打款备注',
            'create_time' => '创建时间',
            'check_time' => '审核时间',
            'finace_ok_time' => '打款时间',
            'client_id' => 'ID',
            'client_no' => '蜜播ID',
            'real_name' => '真实姓名',
            'alipay_no' => '支付宝账号',
            'nick_name' => '用户名',
            'fail_status' => '打款失败状态',
        ];
    }

}
