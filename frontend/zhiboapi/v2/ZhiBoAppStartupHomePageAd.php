<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 11:00
 */

namespace frontend\zhiboapi\v2;
use frontend\business\ActivityUtil;
use frontend\zhiboapi\IApiExcute;


class ZhiBoAppStartupHomePageAd implements IApiExcute
{
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $app_id = $dataProtocal['app_id'];
        if(!isset($app_id) || empty($app_id))
        {
            $error = '找不到对应的版本信息';
            return false;
        }
        $rst = ActivityUtil::GetAdvertisementList($app_id);
        \Yii::error('广告');
        $rstData['has_data'] = 1;
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $rst;
        return true;
    }
}