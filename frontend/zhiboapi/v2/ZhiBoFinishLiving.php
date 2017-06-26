<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-21
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v2;

use common\components\SystemParamsUtil;
use frontend\business\ApiCommon;
use frontend\business\ClientActiveUtil;
use frontend\business\ClientUtil;
use frontend\business\LivingUtil;
use frontend\business\NiuNiuGameUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateExperienceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ExperienceModifyByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\FinishLivingSaveForReward;
use frontend\zhiboapi\IApiExcute;
use yii\db\Query;
use yii\log\Logger;


/**
 * Class 结束直播
 * @package frontend\zhiboapi\v2
 */
class ZhiBoFinishLiving implements IApiExcute
{

    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        $deviceNo = '';
        $uniqueNo= '';
        $registerType='';
        $deviceType='';
        $outinfomain = '';
        if(!ApiCommon::GetBaseInfoFromProtocol($dataProtocal, $deviceNo, $uniqueNo,$registerType,$deviceType,$error))
        {
            return false;
        }
        $loginInfo = null;
        if(!ApiCommon::GetLoginInfo($uniqueNo,$loginInfo, $error))
        {
            return false;
        }

        if(!LivingUtil::SetFinishLiving($dataProtocal['data']['living_id'],$outinfomain,$error))
        {
            return false;
        }

        //结束直播后把限制人数清空
        LivingUtil::QuitRoomUpdateLivingLimitNum($dataProtocal['data']['living_id']);


        //\Yii::getLogger()->log('attend_user_count=:'.$outinfomain['attend_user_count'].' tickets_num:'.$outinfomain['tickets_num'].' living_time='.$outinfomain['living_time'],Logger::LEVEL_ERROR);
        $rstData['data']['attend_user_count'] = (empty($outinfomain['attend_user_count']) == true)?'0':$outinfomain['attend_user_count'];
        $rstData['data']['tickets_num'] = (empty($outinfomain['tickets_num']) == true)?'0':sprintf('%d',$outinfomain['tickets_num']);//转为整数
        $rstData['data']['living_time'] = (empty($outinfomain['living_time']) == true)?'00:00:00':$outinfomain['living_time'];
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';

        return true;
    }
}