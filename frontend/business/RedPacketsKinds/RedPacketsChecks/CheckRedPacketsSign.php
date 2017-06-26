<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-24
 * Time: 下午10:42
 */

namespace frontend\business\RedPacketsKinds\RedPacketsChecks;

use frontend\business\RedPacketsKinds\IRedPacketsCheck;
use yii\log\Logger;

/**
 * 种子红包，只能打赏自己的愿望，满多少才能用
 * Class CheckRedPacketsForAll
 * @package frontend\business\RedPacketsKinds\RedPacketsChecks
 */
class CheckRedPacketsSign implements IRedPacketsCheck
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
        $pay_money = doubleval($params['pay_money']);
        $packets_money = doubleval($red_packet->packets_money);
        if($packets_money >= $pay_money)
        {
            \Yii::getLogger()->log('pmoney:'.strval($packets_money).' paym:'.strval($pay_money), Logger::LEVEL_ERROR);
            $error = '打赏金额必须大于红包金额';
            return false;
        }
        if($red_packet->packets_type === 260)
        {
            return true;
        }
        return false;
    }
} 