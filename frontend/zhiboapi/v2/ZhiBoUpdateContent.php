<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v2;

use common\components\SystemParamsUtil;
use frontend\business\MultiUpdateContentUtil;
use frontend\zhiboapi\IApiExcute;
use frontend\business\UpdateContentUtil;
use yii\log\Logger;

/**
 * Class 获取最新更新版本
 * @package frontend\meiyuanapi\v2
 */
class ZhiBoUpdateContent implements IApiExcute
{
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        $versionList = UpdateContentUtil::GetNewestUpdateVersion(true);
/*        if(empty($dataProtocal['data']) || !is_array($dataProtocal['data']) || count($dataProtocal['data']) == 0)
        {
            $error = '参数错误，没有要更新的内容';
            return false;
        }*/
        //\Yii::getLogger()->log('data-content:'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
        $appid = $dataProtocal['app_id'];
        //\Yii::getLogger()->log('app_id:'.$appid,Logger::LEVEL_ERROR);
        //$appInnerVersion = $dataProtocal['app_version_inner'];
        $multiVersionList = MultiUpdateContentUtil::GetAllUpdateContent($appid);
        $isMultiVersionStr = SystemParamsUtil::GetSystemParam('mb_mulit_version_module',true,'value1');
        $multiVersions = json_decode($isMultiVersionStr,true);
        $shouldUpdateModuleInfo = $dataProtocal['data'];
        $resultModuleInfo = [];
        foreach($shouldUpdateModuleInfo as $moduleId => $version)
        {
            if(in_array($moduleId,$multiVersions))
            {
                if(isset($multiVersionList[$moduleId]) &&
                    !empty($multiVersionList[$moduleId]) &&
                    intval($multiVersionList[$moduleId]['version']) > intval($version))
                {
                    $resultModuleInfo[$moduleId] = $multiVersionList[$moduleId];
                }
            }
            else
            {
                if(isset($versionList[$moduleId]) &&
                    !empty($versionList[$moduleId]) &&
                    intval($versionList[$moduleId]['version']) > intval($version))
                {
                    $resultModuleInfo[$moduleId] = $versionList[$moduleId];
                }
            }
        }

        $rstData['data_type'] = 'json';
        $rstData['data'] = (empty($resultModuleInfo)?"":$resultModuleInfo);
        return true;
    }
} 