<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/6/12
 * Time: 13:45
 */

namespace frontend\business;


use common\models\StatisticTime;

class StatisticTimeUtil
{
    /**
     * 根据主键获取记录
     * @param $itemKey
     */
    public static function GetRecordByItemKey($itemKey)
    {
        return StatisticTime::findOne(['item_key'=>$itemKey]);
    }
} 