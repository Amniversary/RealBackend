<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/31
 * Time: 10:56
 */

namespace frontend\business\OtherPay;


/**
 * 第三方支付处理结果接口
 * Interface IOtherPayResult
 * @package frontend\business\OtherPay
 */
interface IOtherPayResult
{
    function DoOtherPayResult($params, &$error);
} 