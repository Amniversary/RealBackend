<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-24
 * Time: 下午10:39
 */

namespace frontend\business\RedPacketsKinds;

/**
 * 红包领取接口
 * Interface IRedPacketsGet
 * @package frontend\business\RedPacketsKinds
 */
interface IRedPacketsGet
{
    /**
     * @param $params array 参数不固定
     * @param $error  领取失败原因
     * @return bool 领取是否成功
     */
    function GetRedPackets($params,&$error);
} 