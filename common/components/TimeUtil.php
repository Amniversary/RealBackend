<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/25
 * Time: 11:01
 */

namespace common\components;


class TimeUtil
{
    /**
     * 返回两个日期的天数，整数
     * @param $startDate
     * @param string $endDate
     * @return int
     */
    public static function GetDays($endDate,$startDate='')
    {
        if(empty($startDate))
        {
            $startDate = date('Y-m-d');
        }
        $dis = strtotime($endDate) - strtotime($startDate);
        $delayDays = 0;
        if($dis > 0)
        {
            $delayDays = intval($dis / (3600.0 * 24),0);
        }
        return $delayDays;
    }
} 