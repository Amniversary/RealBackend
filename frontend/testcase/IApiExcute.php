<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/16
 * Time: 16:41
 */
namespace frontend\testcase;

interface IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array());
} 