<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-23
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v3;

use frontend\zhiboapi\IApiExcute;


use frontend\zhiboapi\v3\waistcoat\WaistcoatPlot;
/**
 * Class 获取热门直播
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetHotLiving implements IApiExcute
{

    /**
     * 获取热门直播
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $waistcoatPlot = new WaistcoatPlot($dataProtocal);
        $rstData = $waistcoatPlot->DoAction();
        return true;

    }
}