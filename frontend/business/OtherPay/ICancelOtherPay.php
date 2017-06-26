<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/4
 * Time: 13:10
 */

namespace frontend\business\OtherPay;


interface ICancelOtherPay
{
    function CancelPay($params,&$error);
} 