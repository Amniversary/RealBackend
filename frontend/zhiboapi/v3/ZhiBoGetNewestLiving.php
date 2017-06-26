<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-26
 * Time: 上午11:30
 */

namespace frontend\zhiboapi\v3;


use frontend\zhiboapi\IApiExcute;
use frontend\zhiboapi\v3\waistcoat\WaistcoatPlot;



/**
 * Class 获取最新直播
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetNewestLiving implements IApiExcute
{

    /**
     * 获取最新直播
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $waistcoatPlot = new WaistcoatPlot($dataProtocal);
        $rstData = $waistcoatPlot->DoAction();
        return true;
    }
}


