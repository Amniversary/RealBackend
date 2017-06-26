<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/25
 * Time: 14:13
 */

namespace common\components;


class CaculateUtil
{
    /**
     * 根据两点经度和纬度获取距离
     * @param $lng1
     * @param $lat1
     * @param $lng2
     * @param $lat2
     * @return float
     */
        public static function GetDistance($lng1,$lat1, $lng2,$lat2)
        {
            $dis = round(6378.138*2*asin(sqrt(pow(sin(($lat1*pi()/180-$lat2*pi()/180)/2),2)+cos($lat1*pi()/180)*cos($lat2*pi()/180)*pow(sin( ($lng1*pi()/180-$lng2*pi()/180)/2),2)))*1000);
            return $dis;
        }
} 