<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/30
 * Time: 14:22
 */

namespace frontend\business\OtherPay;

/**
 * 检测第三方支付结果
 * Interface ICheckPayResult
 * @package frontend\business\OtherPay
 */
interface ICheckPayResult
{
    function CheckPayResult($passParams, &$error);
} 