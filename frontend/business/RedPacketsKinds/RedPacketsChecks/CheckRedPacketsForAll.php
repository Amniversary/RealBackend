<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-24
 * Time: 下午10:42
 */

namespace frontend\business\RedPacketsKinds\RedPacketsChecks;

use frontend\business\RedPacketsKinds\IRedPacketsCheck;

/**
 * 能打赏所有愿望的红包，无限制
 * Class CheckRedPacketsForAll
 * @package frontend\business\RedPacketsKinds\RedPacketsChecks
 */
class CheckRedPacketsForAll implements IRedPacketsCheck
{
    public function CheckRedPacketsForUse($params,&$error)
    {
        if(!isset($params) || !isset($params['red_packet']))
        {
            $error = '愿望参数不能为空';
            return false;
        }
        $red_packet = $params['red_packet'];
        if($red_packet->status === 1)
        {
            $error = '该红包已被使用';
            return false;
        }
        $curDate = date('Y-m-d');
        if($red_packet->start_time > $curDate)
        {
            $error = '红包未到使用日期';
            return false;
        }
        if( $curDate > $red_packet->end_time)
        {
            $error = '红包已经过期';
            return false;
        }
        if($red_packet->packets_type === 1)
        {
            return true;
        }
        return false;
    }
} 