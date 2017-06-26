<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/30
 * Time: 14:19
 */

namespace frontend\business\GoldPay;

/**
 * 获取第三方支付参数
 * Interface IGetGoldPayParams
 * @package frontend\business\GoldPay
 */
interface IGetGoldPayParams
{
    function GetPayParams($passParam,&$outParams,&$error);
} 