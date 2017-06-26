<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/28
 * Time: 13:40
 */

namespace frontend\zhiboapi\v3;


use frontend\zhiboapi\IApiExcute;

use frontend\zhiboapi\v3\waistcoat\WaistcoatPlot;

/**
 * 获取支付参数列表
 * Class ZhiBoGetPayments
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetPayments implements IApiExcute
{

    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $waistcoatPlot = new WaistcoatPlot( $dataProtocal );
        $rstData = $waistcoatPlot->DoAction();
        return true;
    }
}