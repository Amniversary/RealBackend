<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-22
 * Time: обнГ10:15
 */

namespace common\components;


class StatusUtil
{
    public static function GetStatusList($status,$len = 3)
    {
        $outList = [];
        $status = intval($status);
        for($i = 0; $i < $len; $i ++)
        {
            if(($status & pow(2,$i)) !== 0)
            {
                $outList[] = pow(2,$i);
            }
        }
        return $outList;
    }
} 