<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/12
 * Time: 15:19
 */

namespace common\components\getui\templatefactory;


use yii\log\Logger;

class GeTuiTemplateUtil
{
    /**
     * 获取模板信息
     * @param $template_type
     * @param $data
     * @param $error
     * @return bool
     */
    public static function GetTemplate($template_type,$data,&$error)
    {
        if(!isset($data) || !is_array($data))
        {
            $error = '参数必须是数组';
            return false;
        }
        $configInfo = require(__DIR__.'/MessageTemplateConfig.php');
        if(!isset($configInfo[$template_type]))
        {
            $error = '不存在该模板';
            \Yii::getLogger()->log($error.' :'.$template_type,Logger::LEVEL_ERROR);
            return false;
        }
        $templateClass = 'common\\components\\getui\\templatefactory\\'.$configInfo[$template_type];
        if(!class_exists($templateClass,true))
        {
            $error = '模板类不存在';
            \Yii::getLogger()->log($error.' :'.$templateClass,Logger::LEVEL_ERROR);
            return false;
        }
        $handler = new $templateClass;
        return $handler->GetTemplate($data);
    }
} 