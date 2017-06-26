<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/7/18
 * Time: 15:00
 */

namespace common\components;


use Pili\Hub;
use yii\log\Logger;

class QiNiuUtil
{
    private static $instance = null;

    /**
     * 获取七牛实例
     * @return null|Hub
     */
    public static function GetInstance()
    {
        //return new Hub();
        if(self::$instance === null)
        {
            $lock = new PhpLock('qiniu_instance');
            $lock->lock();
            if(self::$instance === null)
            {
                self::$instance = new Hub();
            }
            $lock->unlock();
        }
        return self::$instance;
    }

    /**
     * 创建七牛直播流
     * @param $title
     * @param $error
     * @return bool|\Pili\Stream
     */
    public static function CreateStream($title,&$error)
    {
        $instance = self::GetInstance();

        $info = $instance->createStream($title,NULL,NULL,$error);
        if($info === false)
        {
            $ejson = json_decode($error);
            if($ejson->error === 'duplicated content')
            {
                return self::GetStreamByStreamId(sprintf('z1.%s.%s',HUB,$title),$error);
            }
            return false;
        }
        $str = $info->toJSONString();
        return json_decode($str,true);
    }

    /**
     * 根据流id获取直播状态
     * @param $stream_id
     * @param $error
     */
    public static function QueryStatus($stream_id,&$error)
    {
        $instance = self::GetInstance();
        $info = $instance->GetStreamStatus($stream_id,$error);
        if($info === false)
        {
            return false;
        }
        return $info;
    }

    /**
     * 根据流id获取流信息
     * @param $stream_id
     * @param $error
     * @return bool|\Pili\Stream
     */
    public static function GetStreamByStreamId($stream_id,&$error)
    {
        $instance = self::GetInstance();
        $rst = $instance->getStream($stream_id,$error);
        if($rst === false)
        {
            return false;
        }
        $str = $rst->toJSONString();
        return json_decode($str,true);
    }
} 