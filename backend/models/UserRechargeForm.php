<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/4
 * Time: 11:08
 */

namespace backend\models;


use yii\base\Model;

class UserRechargeForm extends Model
{
    public $recharge_id;
    public $user_id;
    public $client_no;
    public $goods_id;
    public $goods_name;
    public $goods_price;
    public $goods_num;
    public $bean_num;
    public $pay_money;
    public $status_result;
    public $pay_type;
    public $other_pay_bill;
    public $pay_times;
    public $op_unique_no;
    public $fail_reason;
    public $pay_bill;
    public $create_time;

    /**
     * 获取充值类型
     */
    public static function GetRechargePayStatus($pay_type)
    {
        switch(intval($pay_type))
        {
            case 3:
                $rst = '支付宝支付';
                break;
            case 4:
                $rst = '微信支付';
                break;
            case 5:
                $rst = '连连支付';
                break;
            case 6:
                $rst = '苹果支付';
                break;
            case 100:
                $rst = 'Web微信支付';
                break;
            default:
                $rst = '未知支付类型';
                break;
        }
        return $rst;
    }


    /**
     * 获取支付状态
     */
    public static  function GetRechargeStatus($status_result)
    {
        switch(intval($status_result))
        {
            case 0:
                $rst = '取消支付或支付失败';
                break;
            case 1:
                $rst = '支付中';
                break;
            case 2:
                $rst = '支付成功';
                break;
            default:
                $rst = '未知状态类型';
                break;
        }
        return $rst;
    }
}