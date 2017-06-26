<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-26
 * Time: 上午11:30
 */

namespace frontend\zhiboapi\v2;

use frontend\business\ApiCommon;
use frontend\business\LivingUtil;
use frontend\business\StatisticActiveUserUtil;
use frontend\zhiboapi\IApiExcute;
use frontend\zhiboapi\v2\waistcoat\WaistcoatPlot;
use yii\db\Query;
use yii\log\Logger;


/**
 * Class 获取最新直播
 * @package frontend\zhiboapi\v2
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


