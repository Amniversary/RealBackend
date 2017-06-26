<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/28
 * Time: 12:10
 */

namespace frontend\business;


use common\models\Payment;

class PaymentUtil
{
    /**
     * 获取支付方式列表
     */
    public static function GetPaymentList()
    {
        return Payment::find()->orderBy('order_no asc')->where(['status'=>'1'])->all();
    }

    /**
     * 格式化支付列表
     */
    public static function GetFormatePaymentList($recordList)
    {
        $out = [];
        if(empty($recordList))
        {
            return $out;
        }
        foreach($recordList as $one)
        {
            $ary=[
                'code'=>$one->code,
                'title'=>$one->title,
                'icon'=>$one->remark1
            ];
            $out[] = $ary;
        }
        return $out;
    }
} 