<?php

/**
 * 获取多个直播参数
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/25
 * Time: 15:57
 */
namespace frontend\zhiboapi\v3;

use frontend\business\ClientLivingParamtersUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

class ZhiBoGetMoreQiNiuLivingParams implements IApiExcute
{

    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $uniqueNo = $dataProtocal['data']['unique_no'];
        $model = ClientLivingParamtersUtil::GetMoreClientLivingParamtersByUniqueNo($uniqueNo,$error);
        if($model === false)
        {
            \Yii::getLogger()->log('获取直播参数失败'.$error,Logger::LEVEL_ERROR);
            return false;
        }
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
        $rstData['data'] = $model;
        return true;
    }
}