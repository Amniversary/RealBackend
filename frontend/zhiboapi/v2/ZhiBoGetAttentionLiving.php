<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-23
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v2;

use common\components\SystemParamsUtil;
use frontend\business\ApiCommon;
use frontend\business\AttentionUtil;
use frontend\business\ClientUtil;
use frontend\business\LivingUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\FinishLivingSaveForReward;
use frontend\zhiboapi\IApiExcute;
use yii\db\Query;
use yii\log\Logger;
use frontend\zhiboapi\v2\waistcoat\WaistcoatPlot;

/**
 * Class 获取个人关注的直播
 * @package frontend\zhiboapi\v2
 */
class ZhiBoGetAttentionLiving implements IApiExcute
{

    /**
     * 获取个人关注的直播
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $waistcoatPlot = new WaistcoatPlot($dataProtocal);
        $rstData = $waistcoatPlot->DoAction();
        return true;
    }
}