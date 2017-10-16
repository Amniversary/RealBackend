<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/10
 * Time: 下午5:15
 */

namespace frontend\api;


interface IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = []);
}