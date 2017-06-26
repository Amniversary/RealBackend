<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/20
 * Time: 10:36
 */

namespace frontend\zhiboapi\v3;


use common\components\SystemParamsUtil;
use frontend\business\MultiUpdateContentUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

class ZhiBoVersionCheck implements IApiExcute
{

    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        //\Yii::getLogger()->log('data:'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
        $app_id = $dataProtocal['app_id'];
        $device_type = $dataProtocal['device_type'];
        $version = $dataProtocal['app_version_inner'];
        if(!isset($device_type))
        {
            $error = '设备类型不能为空';
            return false;
        }
        if(!isset($version))
        {
            $error = '版本参数不能为空';
            return false;
        }
        $status = 1;
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
        //\Yii::getLogger()->log('module:'.$module_id,Logger::LEVEL_ERROR);
        //\Yii::getLogger()->log('version:'.$version,Logger::LEVEL_ERROR);
        if(!MultiUpdateContentUtil::CheckVersionInCheck($app_id,$module_id,$version))
        {
            $status = 2;
        }

        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
        $rstData['data'] = [
            'status'=>$status
        ];

        return true;
    }
} 