<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/10
 * Time: 9:58
 */

namespace frontend\zhiboapi\v3;


use frontend\business\ActivityUtil;
use frontend\business\ApiCommon;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 获取分享信息接口 hlq
 * Class ZhiBoGetAppShareInfo
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetAppShareInfo implements IApiExcute
{
    /**
     * @param $dataProtocal
     * @param $rstData
     * @param $error
     * @param array $extendData
     * @return bool
     */
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $unique_no = $dataProtocal['data']['unique_no'];
        $activity_type = $dataProtocal['data']['activity_type'];


        if(!ApiCommon::GetLoginInfo($unique_no,$LoginInfo,$error))
        {
            return false;
        }
        if(empty($activity_type) && $activity_type > 1)
        {
            $error = '活动类型不能为空';
            return false;
        }
        $activity_share_info = ActivityUtil::GetActivityShareInfoByType($activity_type);
        if(!isset($activity_share_info) || empty($activity_share_info))
        {
            $error = '活动类型的分享信息不存在';
            \Yii::getLogger()->log('分享信息  $activity_type==='.$activity_type.'    $unique_no===:'.$unique_no,Logger::LEVEL_ERROR);
            return false;
        }

        $rstData['has_data'] = '0';
        $rstData['data_type'] = 'string';
        $rstData['data'] = $activity_share_info;
        return true;
    }
} 