<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/8
 * Time: 9:57
 */

namespace common\components;

use yii\log\Logger;
class ClearCacheHelper
{
    /*
     *清除热门直播列表的缓存
     */
    public static function ClearHotLivingDataCache()
    {
        for ( $i = 1; $i <= 30; $i++ )
        {
            $key = "mb_api_hot_living_list_$i";
            if( \Yii::$app->cache->get($key) )
            {
                \Yii::$app->cache->delete($key);
            }
        }
    }
}