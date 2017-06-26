<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/28
 * Time: 14:31
 */

namespace frontend\zhiboapi\v3;


use common\components\SystemParamsUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

class ZhiBoGetNoLoginSystemParams implements IApiExcute
{


    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';

        $codeList = $dataProtocal['data']['code'];
        //\Yii::getLogger()->log('Code:'.var_export($codeList,true),Logger::LEVEL_ERROR);
        $out = SystemParamsUtil::GetNoLoginSystemOtherParams($codeList);
    
        $rstData['has_data']='1';
        $rstData['data_type']="jsonarray";
        $rstData['data']=$out;
        return true;
    }
} 