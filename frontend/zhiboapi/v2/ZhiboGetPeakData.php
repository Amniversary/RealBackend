<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/9
 * Time: 11:04
 */

namespace frontend\zhiboapi\v2;

use frontend\business\StatisticActiveUserUtil;
use common\components\PhpLock;
use frontend\business\ApiCommon;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;
use frontend\business\NiuNiuGameUtil;

/**
 *
 * @package frontend\zhiboapi\v2
 */
class ZhiBoGetPeakData implements IApiExcute
{

    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        //\Yii::getLogger()->log("ZhiBoGetPeakData===>". var_export($dataProtocal, true),Logger::LEVEL_ERROR);
        $error = '';
        $unique_no = $dataProtocal['data']['unique_no'];

        $loginInfo = null;
        if(!ApiCommon::GetLoginInfo($unique_no,$loginInfo, $error))
        {
            return false;
        }
        $user_id  = $loginInfo['user_id'];



        //\yii::$app->cache->delete("mb_peakdata");
        $PeakData = false;// \yii::$app->cache->get('mb_peakdata_'.$unique_no.'');

        if(empty($PeakData))
        {
            $lock = new PhpLock('get_peakdata_'.$unique_no.'');
            $lock->lock();

            //\yii::$app->cache->delete("mb_peakdata");
            $PeakData = false;//\yii::$app->cache->get('mb_peakdata_'.$unique_no.'');

            if(empty($PeakData))
            {

                //获得周榜的前30位主播
                $charm_week = StatisticActiveUserUtil::WeekLivingMaster();
                //获得总榜的前30位主播
                $charm_total = StatisticActiveUserUtil::TotalLivingMaster();
                //获得周榜送礼的前30位
                $tyrant_week = StatisticActiveUserUtil::WeekGift();
                //获得总榜送礼的前30位
                $tyrant_total = StatisticActiveUserUtil::TotalGift();
                //获得主播自己当周的魅力值（票数）
                $self_charm_week = StatisticActiveUserUtil::SelfCharmWeek($user_id);
                //获得主播自己当周的财富值（票数）
                $self_tyrant_week = StatisticActiveUserUtil::SelfTyrantWeek($user_id);
                //获得主播自己的总票数和送出的送票数
                $self_charm_total = StatisticActiveUserUtil::SelfCharmTotal($user_id);

                //游戏的周榜
                $game_week =  NiuNiuGameUtil::WeekGameResultRanking();
                //游戏的总榜
                $game_total = NiuNiuGameUtil::TotalGameResultRanking();

                $rst = [];

                //根据相应的主播ID的到相应的头像，昵称，性别
                $rst['charm']['week'] = $charm_week;
                $rst['charm']['total'] = $charm_total;
                $rst['tyrant']['week']  = $tyrant_week;
                $rst['tyrant']['total'] = $tyrant_total;

                $rst['game']['week']  = $game_week;
                $rst['game']['total'] = $game_total;

                $rst['self'] = $self_charm_total;
                if(!(gettype($self_tyrant_week) === 'boolean'))
                {
                    $rst['self'] += $self_tyrant_week;
                }
                else
                {
//                    $self_tyrant_week = ['tyrant_week'=>];
                    $rst['self']['tyrant_week'] = 0;
                }
                if(!(gettype($self_charm_week) === 'boolean'))
                {
                    $rst['self'] += $self_charm_week;
                }
                else
                {
//                    $self_tyrant_week = ['charm_week'=>0];
                    $rst['self']['charm_week'] = 0;
                }
                $gmaeweek = NiuNiuGameUtil::SelfWeekGameResult( $user_id );
                if( $gmaeweek ){
                    $rst['self']['game_week'] = $gmaeweek;
                }else
                {
                    $rst['self']['game_week'] = 0;
                }
                $gmaetotal = NiuNiuGameUtil::SelfTotalGameResult( $user_id );
                if( $gmaetotal ){
                    $rst['self']['game_total'] = $gmaetotal;
                }else{
                    $rst['self']['game_total'] =0;
                }
                $rst1 = json_encode($rst);

                //设置缓存
                \yii::$app->cache->set('mb_peakdata_'.$unique_no.'',$rst1,600);
                $rst = $this->attention($user_id,$rst);
                $rstData['data'] = $rst;

            }
            else
            {
                $PeakDatas = $this->object_array(json_decode($PeakData));
                $PeakDatass = $this->attention($user_id,$PeakDatas);
                $rstData['data'] = $PeakDatass;
            }
            $lock->unlock();
        }
        else
        {
            $PeakDatas = $this->object_array(json_decode($PeakData));
            $PeakDatass = $this->attention($user_id,$PeakDatas);
            $rstData['data'] = $PeakDatass;
        }

        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';

        return true;
    }

    /**
     * 将对象转换成数组
     * @param $array
     * @return array
     */
    function object_array($array)
    {
        if(is_object($array))
        {
            $array = (array)$array;
        }
        if(is_array($array))
        {
            foreach($array as $key=>$value)
            {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }
    /**
     * 在缓存中加入‘is_attention’
     * 会在同一个的用户登入时执行
     */
    function attention($user_id,$PeakDatas)
    {
        //关注
        $charm_week_attention = StatisticActiveUserUtil::WeekLivingMasterAttention($user_id);
        //关注
        $charm_total_attention = StatisticActiveUserUtil::TotalLivingMasterAttention($user_id);
        //关注
        $tyrant_week_attention = StatisticActiveUserUtil::WeekGiftAttention($user_id);
        //关注
        $tyrant_total_attention = StatisticActiveUserUtil::TotalGiftAttention($user_id);

        //user_id关注的人
        $attentionList = StatisticActiveUserUtil::GetUserAttention( $user_id );

        foreach ( $attentionList as $value ){

            $attentionList[] = $value['friend_user_id'];
        }

        foreach($PeakDatas['charm']['week'] as $vv)
        {
            $s = array_search($vv,$PeakDatas['charm']['week']);
            $vv['is_attention'] =  $charm_week_attention[$s]['is_attention'];
            $PeakDatas['charm']['week'][$s] = $vv;
        }
        foreach($PeakDatas['charm']['total'] as $vv)
        {
            $s = array_search($vv,$PeakDatas['charm']['total']);
            $vv['is_attention'] =  $charm_total_attention[$s]['is_attention'];
            $PeakDatas['charm']['total'][$s] = $vv;
        }
        foreach($PeakDatas['tyrant']['week'] as $vv)
        {
            $s = array_search($vv,$PeakDatas['tyrant']['week']);
            $vv['is_attention'] =  $tyrant_week_attention[$s]['is_attention'];
            $PeakDatas['tyrant']['week'][$s] = $vv;
        }
        foreach($PeakDatas['tyrant']['total'] as $vv)
        {
            $s = array_search($vv,$PeakDatas['tyrant']['total']);
            $vv['is_attention'] =  $tyrant_total_attention[$s]['is_attention'];
            $PeakDatas['tyrant']['total'][$s] = $vv;
        }



        foreach($PeakDatas['game']['week'] as $key=>$vv)
        {
            if( in_array( $vv['user_id'],$attentionList ) )
            {
                $PeakDatas['game']['week'][$key]['is_attention'] =  1;
            }else
            {
                $PeakDatas['game']['week'][$key]['is_attention'] =  0;
            }
        }

        foreach($PeakDatas['game']['total'] as $key=>$vv)
        {
            if( in_array( $vv['user_id'],$attentionList ) )
            {
                $PeakDatas['game']['total'][$key]['is_attention'] =  1;
            }else
            {
                $PeakDatas['game']['total'][$key]['is_attention'] =  0;
            }
        }

        return $PeakDatas;
    }




}