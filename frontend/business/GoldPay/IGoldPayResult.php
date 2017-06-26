<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/31
 * Time: 10:56
 */

namespace frontend\business\GoldPay;


/**
 * 第三方支付处理结果接口
 * Interface IPayResult
 * @package frontend\business\GoldPay
 */
interface IGoldPayResult
{
    function DoOtherPayResult($params, &$error);
} 