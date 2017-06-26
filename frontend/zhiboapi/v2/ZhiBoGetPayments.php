<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/28
 * Time: 13:40
 */

namespace frontend\zhiboapi\v2;


use common\components\SystemParamsUtil;
use frontend\business\ApiCommon;
use frontend\business\MultiUpdateContentUtil;
use frontend\business\PaymentsUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;
use frontend\zhiboapi\v2\waistcoat\WaistcoatPlot;

class ZhiBoGetPayments implements IApiExcute
{

    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $waistcoatPlot = new WaistcoatPlot( $dataProtocal );
        $rstData = $waistcoatPlot->DoAction();
        return true;
    }
}