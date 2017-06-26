<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-24
 * Time: 下午10:33
 */
namespace frontend\business\RedPacketsKinds;

/**
 * 红包使用检测接口
 * Interface IRedPacketsCheck
 * @package frontend\business\RedPacketsKinds
 */
interface IRedPacketsCheck
{
    /**
     * @param $params array 传递的参数，根据每种红包类别的需要传递，不固定
     * @param $error 返回错误
     * @return bool
     */
    function CheckRedPacketsForUse($params,&$error);
} 