<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/30
 * Time: 14:22
 */

namespace frontend\business\OtherGoldPay;

/**
 * 检测第三方支付结果
 * Interface ICheckPayResult
 * @package frontend\business\OtherPay
 */
interface ICheckGoldPayResult
{
    function CheckPayResult($passParams, &$error);
} 