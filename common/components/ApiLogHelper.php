<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/10/18
 * Time: 15:39
 */

namespace common\components;



class ApiLogHelper
{
    /**
     * 获取api字段映射表
     * @return array
     */
    public static function GetApiLogFields()
    {
        $file = __DIR__.'/../config/log_field_config.php';
        return require($file);
    }

    /**
     * 获取apiname映射表
     * @return array
     */
    public static function GetApiNameConfig()
    {
        $file = __DIR__.'/../config/log_apiname_config.php';
        return require($file);
    }

    /**
     * @param array $apiLog
     * @return mixed array or false 如果false
     */
    public static function CompressApiLog($apiLog)
    {
        if(!is_array($apiLog))
        {
            return false;
        }
        $rst = [];
        $fieldsConfig = self::GetApiLogFields();
        $apiNameConfig = self::GetApiNameConfig();
        foreach($apiLog as $k => $v)
        {
            if($k === 'remark2')
            {
                continue;
            }
            if($k == 'fun_id')
            {
                $tmp = (isset($apiNameConfig[$v])?$apiNameConfig[$v]:$v);
            }
            else
            {
                $tmp = $v;
            }
            $rst[$fieldsConfig[1][$k]] = $tmp;
        }
        return $rst;
    }

    /**
     * 解压apilog数组
     * @param $apiLog array
     * @return mixed array or false
     */
    public static function DecompressApiLog($apiLog)
    {

    }
} 