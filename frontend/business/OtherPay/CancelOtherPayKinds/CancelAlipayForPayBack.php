<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/4
 * Time: 13:12
 */

namespace frontend\business\OtherPay\CancelOtherPayKinds;


use frontend\business\OtherPay\ICancelOtherPay;

class CancelAlipayForPayBack implements ICancelOtherPay
{
    public function CancelPay($params,&$error)
    {
        if(!isset($params))
        {
            $error = '参数不能为空';
            return;
        }
        if(!isset($params['bill_no']) || empty($params['bill_no']))
        {
            $error ='账单号不能为空';
            return false;
        }
        return true;
    }
} 