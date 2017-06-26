<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class GetCashForm extends Model
{
    //'balance','cash_money','status','first_get_money','identity_no','real_name','card_no','bank_name','check_time'
    public $get_cash_id;
    public $cash_money;
    public $status;
    public $first_get_money;
    public $identity_no;
    public $balance;
    public $real_name;
    public $card_no;
    public $bank_name;
    public $check_time;
    public $user_id;


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
            'get_cash_id'=>'提现记录id',
            'cash_money'=>'提现金额',
            'status'=>'审核状态',
            'first_get_money'=>'首次提现',
            'identity_no'=>'身份证号',
            'balance'=>'账户余额',
            'check_time'=>'审核时间',
            'real_name'=>'姓名',
            'card_no'=>'银行卡号',
            'bank_name'=>'银行名称',
            'user_id'=>'用户id',
        ];
    }

    /**
     * 获取状态名称
     * @return string
     */
    public static function GetStatusName($status)
    {
        $rst = '';
        switch(intval($status))
        {
            case 1:
                $rst = '已受理';
                break;
            case 2:
                $rst = '已审核';
                break;
            case 3:
                $rst = '已打款';
                break;
            case 4:
                $rst = '审核被拒绝';
                break;
            default:
                $rst = '未知类型';
                break;
        }
        return $rst;
    }

}
