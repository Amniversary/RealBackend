<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/18
 * Time: 18:46
 */

namespace backend\components;

class ExitUtil
{
    /**
     * 退出页面
     * @param $msg
     */
    public static function  ExitWithMessage($msg)
    {
        echo sprintf('<H1>%s</H1>',$msg);
        exit;
    }
} 