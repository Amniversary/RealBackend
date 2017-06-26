<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-24
 * Time: 下午10:42
 */

namespace frontend\business\RedPacketsKinds\RedPacketsChecks;

use frontend\business\RedPacketsKinds\IRedPacketsCheck;
use frontend\business\WishTypeUtil;
use frontend\business\WishUtil;

/**
 * 种子红包，只能打赏自己的愿望，无条件
 * Class CheckRedPacketsForAll
 * @package frontend\business\RedPacketsKinds\RedPacketsChecks
 */
class CheckRedPacketsRewardSelf implements IRedPacketsCheck
{
    public function CheckRedPacketsForUse($params,&$error)
    {
        if(!isset($params) || !isset($params['red_packet']))
        {
            $error = '愿望参数不能为空';
            return false;
        }
        if(!isset($params['wish']))
        {
            $error = '愿望参数不能为空';
            return false;
        }
        $wish = $params['wish'];
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
        if($red_packet->user_id !== $wish->publish_user_id)
        {
            $error = '种子红包紧打赏自己的愿望';
            return false;
        }
        if($red_packet->packets_type === 257)
        {
            return true;
        }
        return false;
    }
} 