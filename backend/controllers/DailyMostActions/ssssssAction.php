<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/29
 * Time: 21:09
 */

namespace backend\controllers\DailyMostActions;


use backend\business\StatisticActiveUserUtil;
use common\models\Client;
use common\models\StatisticDailyLivingMaster;
use frontend\business\ApiCommon;
use frontend\business\ClientUtil;
use yii\base\Action;
use yii\db\Query;

class ssssssAction extends Action
{
    public function run()
    {
        $user_id = 1;
        //获得周榜的前30位主播
        $charm_week = StatisticActiveUserUtil::WeekLivingMaster();

        //关注
        $charm_week_attention = StatisticActiveUserUtil::WeekLivingMasterAttention($user_id);

        //获得总榜的前30位主播
        $charm_total = StatisticActiveUserUtil::TotalLivingMaster();

        //关注
        $charm_total_attention = StatisticActiveUserUtil::TotalLivingMasterAttention($user_id);

        //获得周榜送礼的前30位
        $tyrant_week = StatisticActiveUserUtil::WeekGift();

        //关注
        $tyrant_week_attention = StatisticActiveUserUtil::WeekGiftAttention($user_id);

        //获得总榜送礼的前30位
        $tyrant_total = StatisticActiveUserUtil::TotalGift();

        //关注
        $tyrant_total_attention = StatisticActiveUserUtil::TotalGiftAttention($user_id);

        $rst = [];

        $rst['charm']['week'] = $charm_week;
        $rst['charm']['total'] = $charm_total;
        $rst['tyrant']['week'] = $tyrant_week;
        $rst['tyrant']['total'] = $tyrant_total;

        $rst1 = json_encode($rst);

        //设置缓存
        \yii::$app->cache->set('mb_peakdata_'.$user_id.'',$rst1,3600);

        foreach($charm_week as $vv)
        {

            $s = array_search($vv,$charm_week);

            $vv['is_attention'] =  $charm_week_attention[$s]['is_attention'];
            $charm_week[$s] = $vv;
        }

        foreach($charm_total as $vv)
        {

            $s = array_search($vv,$charm_total);

            $vv['is_attention'] =  $charm_total_attention[$s]['is_attention'];
            $charm_total[$s] = $vv;
        }

        foreach($tyrant_week as $vv)
        {

            $s = array_search($vv,$tyrant_week);

            $vv['is_attention'] =  $tyrant_week_attention[$s]['is_attention'];
            $tyrant_week[$s] = $vv;
        }

        foreach($tyrant_total as $vv)
        {

            $s = array_search($vv,$tyrant_total);

            $vv['is_attention'] =  $tyrant_total_attention[$s]['is_attention'];
            $tyrant_total[$s] = $vv;
        }

        //根据相应的主播ID的到相应的头像，昵称，性别



        $rst['charm']['week'] = $charm_week;
        $rst['charm']['total'] = $charm_total;
        $rst['tyrant']['week'] = $tyrant_week;
        $rst['tyrant']['total'] = $tyrant_total;


        $PeakData = \yii::$app->cache->get('mb_peakdata_'.$user_id.'');

        $PeakDatas = $this->object_array(json_decode($PeakData));



        foreach($PeakDatas['charm']['week'] as $vv)
        {

            $s = array_search($vv,$PeakDatas['charm']['week']);
            $vv['is_attention'] =  $charm_week_attention[$s]['is_attention'];
            $PeakDatas['charm']['week'][$s] = $vv;
        }


        print_r("<pre>");
        print_r($rst);
    }


    function object_array($array) {
        if(is_object($array)) {
            $array = (array)$array;
        } if(is_array($array)) {
            foreach($array as $key=>$value) {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }
}