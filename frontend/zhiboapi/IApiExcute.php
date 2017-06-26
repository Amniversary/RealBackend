<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:33
 */
namespace frontend\zhiboapi;

interface IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array());
} 