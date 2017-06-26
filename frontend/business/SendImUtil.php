<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 13:34
 */

namespace frontend\business;


use common\components\tenxunlivingsdk\TimRestApi;
use yii\helpers\Console;
use yii\log\Logger;

class SendImUtil
{
    /**
     * 处理im需要的参数
     * @param $params //im参数
     * @param $key_word  //对应处理名
     * @param $error
     * @return bool
     */
    public static function GetSendImParams($params,$key_word,&$error)
    {
        $configFile = __DIR__.'/SendImMessage/SendImConfig.php';
        if(!file_exists($configFile)) //检测文件目录是否存在  true false
        {
            $error = '系统错误，找不到im配置文件';
            \Yii::getLogger()->log($error.' :'.$configFile,Logger::LEVEL_ERROR);
            return false;
        }

        $configFile = require($configFile);
        if(!isset($configFile[$key_word]))
        {
            $error = '未实现的im方式，找不到对应处理类';
            \Yii::getLogger()->log($error.' :'.$key_word,Logger::LEVEL_ERROR);
            return false;
        }

        $dealClass = $configFile[$key_word];
        if(!class_exists($dealClass))
        {
            $error = '对应im处理类不存在';
            \Yii::getLogger()->log($error.' :'.$dealClass,Logger::LEVEL_ERROR);
            return false;
        }

        $instance = new $dealClass;
        if(!$instance->excute_im($params, $error))
        {
            return false;
        }
        return true;
    }

    /**
     * 发送腾云IM消息，3次重发机制
     * @param $user_id
     * @param $group_id
     * @param $text
     * @param $error
     * @return bool
     */
    public static function SendImMsg($user_id,$group_id,$text,&$error)
    {
        $i = 0;
        do{
            if(TimRestApi::group_send_group_msg_custom(strval($user_id),strval($group_id),$text,$error))
            {
                return true;
            }
            $i++;
        }while($i < 3);
        return false;
    }
} 