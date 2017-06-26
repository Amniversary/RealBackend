<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/30
 * Time: 14:33
 */

namespace frontend\business\OtherPay\GetPayParamsKinds;


use common\components\llpay\LlpayNotifyUtil;
use common\components\WaterNumUtil;
use frontend\business\OtherPay\IGetPayParams;

class GetLlpayParamsForCheckBankCard implements IGetPayParams
{
    public function GetPayParams($passParam,&$outParams,&$error)
    {
        if(!isset($passParam) || !is_array($passParam))
        {
            $error = '参数异常';
            return false;
        }
        $out = null;
        $llpay_bill_no = WaterNumUtil::GenWaterNum('LLP',true,true,'2016-02-01');
        $llpay_bill_no = str_replace('-','',$llpay_bill_no);
        $llpayConfig = LlpayNotifyUtil::GetLLpayConfig();
        $outParams = [
                'oid_partner'=>$llpayConfig['oid_partner'], // 商户编号
            'key'=>$llpayConfig['key'],
    'no_order'=>$llpay_bill_no,//商户唯一订单号
    'busi_partner'=>'101001',//虚拟商品销售：101001  实物商品销售：109001
    'sign_type'=>'MD5',//签名方式
    'notify_url'=>$llpayConfig['notify_url'], // 异步通知URL
    'name_goods'=>'银行卡验证支付0.01', //商品名称
    'money_order'=>'0.01', // 金额
    'user_info_bind_phone'=>$passParam['phone_no'],  // 绑定的手机号
    'frms_ware_category'=>'2999', // 商品类目代码表
	'user_info_dt_register'=>date('YmdHis'), //注册时间
    'valid_order'=>'30',//订单有效时间 分钟为单位，默认为 10080 分钟（7 天），从创建时间开始，过了此订单有效时间此笔 订单就会被设置为失败状态不能再重新进行支付。
    'no_agree'=>'',// 协议号
    'user_id'=>$passParam['user_id'],//用户ID
    'dt_order'=>date('YmdHis'),//商户订单时间
	'info_order'=>'pay_target=checkbankcard',//订单描述
    'fenxian'=>[
        'frms_ware_category'=>'2009',
        'user_info_mercht_userno'=>$passParam['user_id'],
        'user_info_bind_phone'=>$passParam['phone_no'],
        'user_info_dt_register'=>date('YmdHis'),
        'user_info_full_name'=>'',
        'user_info_id_no'=>'',
        'user_info_id_type'=>'0',
        'user_info_identify_state'=>'1',
        'user_info_identify_type'=>'1'
    ],
        ];
        return true;
    }
} 