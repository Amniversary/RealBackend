<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/25
 * Time: 15:57
 */
namespace frontend\zhiboapi\v3;

use frontend\business\ClientLivingParamtersUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

class ZhiBoGetQiNiuLivingParams implements IApiExcute
{

    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $uniqueNo = $dataProtocal['data']['unique_no'];
        //\Yii::getLogger()->log('unique_id:'.$uniqueNo,Logger::LEVEL_ERROR);
        $model = ClientLivingParamtersUtil::GetClientLivingParamtersByUniqueNo($uniqueNo,$error);
        //\Yii::getLogger()->log('query_error:'.var_export($model,true),Logger::LEVEL_ERROR);
        if($model === false)
        {
            return false;
        }
        unset($model['client_id']);
        //\Yii::getLogger()->log('back:'.var_export($userInfo,true),Logger::LEVEL_ERROR);
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
        $rstData['data'] = $model;
        //\Yii::getLogger()->log('living_info:'.var_export($rstData,true),Logger::LEVEL_ERROR);
        return true;
    }
} 