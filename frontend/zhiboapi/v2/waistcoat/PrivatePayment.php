<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/24
 * Time: 9:35
 */

namespace frontend\zhiboapi\v2\waistcoat;

use common\components\SystemParamsUtil;
use frontend\business\ApiCommon;
use frontend\business\MultiUpdateContentUtil;
use frontend\business\PaymentsUtil;


class PrivatePayment implements IExcute
{
    function action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $app_id = $dataProtocal['app_id'];
        $version = $dataProtocal['app_version_inner'];
        $device_type = $dataProtocal['device_type'];
        $uniqueNo = $dataProtocal['data']['unique_no'];
        $status = 2;
        //\Yii::getLogger()->log('data:'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }
        $isMultiVersionStr = SystemParamsUtil::GetSystemParam('mb_mulit_version_module',true,'value1');
        $multiVersions = json_decode($isMultiVersionStr,true);
        if($device_type == 1)
        {
            $module_id = $multiVersions[0];
        }
        else
        {
            $module_id = $multiVersions[1];
        }
        if(!MultiUpdateContentUtil::CheckVersionInCheck($app_id,$module_id,$version))
        {
            $status = 1;
        }

        $payments = PaymentsUtil::getPaymentsByAppId($app_id, $status);

        $rstData['has_data'] = count($payments) > 0 ? '1' : '0';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $payments;

        return true;
    }
}


