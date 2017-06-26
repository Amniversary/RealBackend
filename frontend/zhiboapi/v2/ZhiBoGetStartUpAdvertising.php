<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-22
 * Time: 中午11:30
 */

namespace frontend\zhiboapi\v2;


use frontend\business\AdImagesUtil;
use frontend\business\ApiCommon;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\UserAdvertistingByTrans;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;


/**
 * Class 获取弹窗广告
 * @package frontend\zhiboapi\vv
 */
class ZhiBoGetStartUpAdvertising implements IApiExcute
{
    /**
     * 获取弹窗广告
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!ApiCommon::GetLoginInfo($dataProtocal['data']['unique_no'],$loginfo,$error)){
            return false;
        }

        $user_adimages = AdImagesUtil::GetUserAdImagesList($loginfo['user_id']);
        $ad_ids = [];
        if($user_adimages)
        {
            foreach($user_adimages as $image)
            {
                $ad_ids[] = $image['ad_id'];
            }
        }
        $transActions = [];
        $ad_info = AdImagesUtil::GetImagesList($ad_ids);
        $date = date('Y-m-d H:i:s');
        $new_info = [];
        if(!empty($ad_info))
        {
            foreach($ad_info as $info)
            {
                if(($date >= $info['start_time']) && ($date <= $info['end_time']))
                {
                    $new_info[] = $info;
                }
                $info['user_id'] = $loginfo['user_id'];
                $transActions[] = new UserAdvertistingByTrans($info);
            }
            if(!RewardUtil::RewardSaveByTransaction($transActions,$outInfo, $error))
            {
                \Yii::getLogger()->log('ZhiBoGetStartUpAdvertising error:'.$error,Logger::LEVEL_ERROR);
                return false;
            }
        }

        $rstData['has_data'] = '0';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $new_info;
        return true;
    }
}