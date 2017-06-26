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
 * 只能专项打赏某个愿望，并且满多少才能用
 * Class CheckRedPacketsForAll
 * @package frontend\business\RedPacketsKinds\RedPacketsChecks
 */
class CheckRedPacketsForWishWithCondition implements IRedPacketsCheck
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
        $other_id = $red_packet->other_id;
        if($other_id !== $wish->wish_id)
        {
            $error = sprintf('该红包紧打赏愿望【%s】',$wish->wish_name);
            return false;
        }
        $pay_money = doubleval($params['pay_money']);
        $over_money_for_use = doubleval($red_packet->over_money_for_use);
        if($over_money_for_use > $pay_money)
        {
            $error = sprintf('该红包满【%s】才能使用', $over_money_for_use);
            return false;
        }
        if($red_packet->packets_type === 32)
        {
            return true;
        }
        return false;
    }
} 