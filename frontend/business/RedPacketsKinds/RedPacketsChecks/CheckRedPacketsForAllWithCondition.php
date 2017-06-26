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
 * Class 能打赏所有愿望的红包，满多少才能用
 * @package frontend\business\RedPacketsKinds\RedPacketsChecks
 */
class CheckRedPacketsForAllWithCondition implements IRedPacketsCheck
{
    public function CheckRedPacketsForUse($params,&$error)
    {
        if(!isset($params) || !isset($params['red_packet']))
        {
            $error = '愿望参数不能为空';
            return false;
        }
        if(!isset($params['pay_money']))
        {
            //打赏金额
            $error = '参数打赏金额不能为空';
            return false;
        }
        $red_packet = $params['$red_packet'];
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
        $pay_money = doubleval($params['pay_money']);
        $over_money_for_use = doubleval($red_packet->over_money_for_use);
        if($over_money_for_use > $pay_money)
        {
            $error = sprintf('该红包满【%s】才能使用', $over_money_for_use);
            return false;
        }
        if($red_packet->packets_type === 2)
        {
            return true;
        }
        else
        {
            $error = '红包类型错误';
        }
        return false;
    }
} 