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
use frontend\business\BillUtil;
use frontend\business\OtherPay\IGetPayParams;
use common\components\alipay\AlipayUtil;
use frontend\business\PersonalUserUtil;
use yii\log\Logger;

class GetLlpayParamsForPayBack implements IGetPayParams
{
    public function GetPayParams($passParam,&$outParams,&$error)
    {
        if(!isset($passParam) || !is_array($passParam))
        {
            $error = '参数异常';
            return false;
        }
        //检测参数
        $fields = ['bill_id','real_back_money','breach_money','last_breach_money','breach_days'];
        $fieldLabels = ['账单id','实际还款金额','违约金额','持续违约金额','违约天数'];
        $len = count($fields);
        for($i =0; $i <$len; $i ++)
        {
            if(!isset($passParam[$fields[$i]]))
            {
                $error = $fieldLabels[$i].'不能为空';
                return false;
            }
        }

        $real_pay_money = $passParam['real_back_money'];
        $bill_id = $passParam['bill_id'];
        $billInfo = BillUtil::GetBillRecordById($bill_id);
        if(!isset($billInfo))
        {
            $error = '找不到账单信息';
            \Yii::getLogger()->log($error.' bill_id'.$bill_id,Logger::LEVEL_ERROR);
            return false;
        }

        $user_id = $billInfo->user_id;
        $user = PersonalUserUtil::GetAccontInfoById($user_id);
        if(!isset($user))
        {
            $error = '用户信息找不到';
            return false;
        }

        $billInfo->pay_bill = WaterNumUtil::GenWaterNum('ZHF-PB-',true,true,'2015-12-30',4);
        $billInfo->pay_times = intval($billInfo->pay_times) + 1;
        if(!$billInfo->save())
        {
            $error = '账单信息保存失败';
            \Yii::getLogger()->log(var_export($billInfo->getErrors(), true),Logger::LEVEL_ERROR);
            return false;
        }
        $body = sprintf('pay_target=payback&bill_id=%s&real_back_money=%s&breach_money=%s&last_breach_money=%s&breach_days=%s',
            $bill_id,
            $passParam['real_back_money'],
            $passParam['breach_money'],
            $passParam['last_breach_money'],
            $passParam['breach_days']
            );
        $out_trade_no = $billInfo->pay_bill;// $out['bill_no'];
        $llpayConfig = LlpayNotifyUtil::GetLLpayConfig();
        $outParams = [
            'oid_partner'=>$llpayConfig['oid_partner'], // 商户编号
            'key'=>$llpayConfig['key'],
            'no_order'=>$out_trade_no,//商户唯一订单号
            'busi_partner'=>'101001',//虚拟商品销售：101001  实物商品销售：109001
            'sign_type'=>'MD5',//签名方式
            'notify_url'=>$llpayConfig['notify_url'], // 异步通知URL
            'name_goods'=>'连连支付还款', //商品名称
            'money_order'=>strval($real_pay_money), // 金额
            'user_info_bind_phone'=>$user->phone_no,  // 绑定的手机号
            'frms_ware_category'=>'2999', // 商品类目代码表
            'user_info_dt_register'=>date('YmdHis'), //注册时间
            'valid_order'=>'30',//订单有效时间 分钟为单位，默认为 10080 分钟（7 天），从创建时间开始，过了此订单有效时间此笔 订单就会被设置为失败状态不能再重新进行支付。
            'no_agree'=>'',// 协议号
            'user_id'=>$user_id,//用户ID
            'dt_order'=>date('YmdHis'),//商户订单时间
            'info_order'=>$body,//订单描述
            'fenxian'=>[
                'frms_ware_category'=>'2009',
                'user_info_mercht_userno'=>strval($user_id),
                'user_info_bind_phone'=>$user->phone_no,
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