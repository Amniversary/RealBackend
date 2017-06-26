<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/5
 * Time: 13:45
 */
namespace common\components\getui\GetuiVersions;

use yii\log\Logger;

class GetuiVersionUtil
{
    public static function GetGetuiVersions($app_id,&$error)
    {
        if(!isset($app_id) || empty($app_id))
        {
            $error = '版本参数不能为空';
            return false;
        }
        $configFile = require(__DIR__.'/VersionsConfig.php');
        if(!isset($configFile[$app_id]))
        {
            $error = '该版本模板不存在';
            \Yii::getLogger()->log($error.' :'.$app_id,Logger::LEVEL_ERROR);
            return false;
        }
        $paramsFile = 'common\\components\\getui\\GetuiVersions\\'.$configFile[$app_id];
        if(!class_exists($paramsFile))
        {
            $error = '对应版本处理类不存在';
            \Yii::getLogger()->log($error.' :'.$paramsFile,Logger::LEVEL_ERROR);
            return false;
        }
        $handler = new $paramsFile;
        return $handler;
    }
} 