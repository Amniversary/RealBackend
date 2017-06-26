<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/10/14
 * Time: 13:59
 */
namespace common\components\rebots;

use common\components\PhpLock;

class RebotsUtil
{
    private  static $autoNickNameHandler = null;

    /**
     * 获取昵称
     * @param int $type 0表示三个字昵称；1表示2个字昵称 2表示昵称2或3个字
     * @param bool $auto 默认自动确认类型，如果要固定type请将此属性设置为false
     * @return string
     * @throws \yii\base\Exception
     */
    public static function GetNickName($type = 0,$auto =true)
    {
        if(static::$autoNickNameHandler == null)
        {
            $lock = new PhpLock('mibo_gen_rebots_nick_name');
            $lock ->lock();
            if(static::$autoNickNameHandler == null)
            {
                static::$autoNickNameHandler = new AutoNickName();
            }
            $lock->unlock();
        }
        if(!(static::$autoNickNameHandler instanceof AutoNickName))
        {
            throw new \yii\base\Exception('not AutoNickName object,type error');
        }
        if($auto === true)
        {
            $type = rand(0,2);
        }
        return static::$autoNickNameHandler->GetName($type);
    }
} 