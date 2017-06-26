<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/17
 * Time: 16:31
 */

namespace frontend\business;


use common\components\PhpLock;
use yii\db\Query;

class ActivityStatisticUtil {

    /**
     * 获取女神节活动数据
     * @return mixed
     */
    public static function ActivityGirlInfo()
    {
        $girls = (new Query())
            ->select(['user_id','client_no','nick_name','IFNULL(nullif(icon_pic,\'\'),pic) as pic','value'])
            ->from('mb_activity_girl ag')
            ->innerJoin('mb_client c','c.client_id = ag.user_id and type = 1')
            ->orderBy(['value'=>SORT_DESC])
            ->limit(8)
            ->all();

        $richer = (new Query())
            ->select(['user_id','client_no','nick_name','IFNULL(nullif(icon_pic,\'\'),pic) as pic','value'])
            ->from('mb_activity_girl ag')
            ->innerJoin('mb_client c','c.client_id = ag.user_id and type = 2')
            ->orderBy(['value'=>SORT_DESC])
            ->limit(8)
            ->all();
        $max = [];
        $mat = [];
        for($i = 0; $i < 3; $i++)
        {
            $max[] = $girls[$i];
            unset($girls[$i]);
            $mat[] = $richer[$i];
            unset($richer[$i]);
        }
        $rst['girls']['data'] = array_values($girls);
        $rst['girls']['max'] = $max;
        $rst['richer']['data'] = array_values($richer);
        $rst['richer']['max'] = $mat;

        return $rst;
    }

    /**
     * 设置缓存女神节活动
     * @return string
     */
    public static function GirlCache()
    {
        $cnt = \Yii::$app->cache->get('activity_girl_cache');
        if($cnt === false) {
            $lock = new PhpLock('activity_girl');
            $lock->lock();
            $cnt = \Yii::$app->cache->get('activity_girl_cache');
            if($cnt === false) {
                $rst = self::ActivityGirlInfo();
                $pStr = serialize($rst);
                \Yii::$app->cache->set('activity_girl_cache',$pStr,5 * 60);
            }else{
                $rst = unserialize($cnt);
            }
            $lock->unlock();
        }else{
            $rst = unserialize($cnt);
        }
        return json_encode($rst);
    }
} 