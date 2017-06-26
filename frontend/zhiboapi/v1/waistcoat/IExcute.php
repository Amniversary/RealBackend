<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 15:05
 */

namespace frontend\zhiboapi\v1\waistcoat;


interface IExcute
{
    function action($dataProtocal, &$rstData,&$error, $extendData= array());
}