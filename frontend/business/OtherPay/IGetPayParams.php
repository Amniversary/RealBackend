<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/30
 * Time: 14:19
 */

namespace frontend\business\OtherPay;

/**
 * 获取第三方支付参数
 * Interface IGetPayParams
 * @package frontend\business\OtherPay
 */
interface IGetPayParams
{
    function GetPayParams($passParam,&$outParams,&$error);
} 