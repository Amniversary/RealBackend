<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/13
 * Time: 21:26
 */

namespace frontend\business;


use yii\log\Logger;

class NiuNiuGameGrabBankerUtil {
    /**
     *  庄家比较大小
     * -1 到 9 ，-1 没有 0 牛牛  1 ~9 牛1到牛9
     * @param $seat_num
     * @param $game_id
     * @param $multiple
     * @param $error
     * @return array|bool
     */
    public static function DoGrabBanker($living_id,$seat_num,$game_id,$multiple,&$error)
    {
        if(!isset($seat_num) || empty($seat_num))
        {
            \Yii::getLogger()->log('位置id不存在',Logger::LEVEL_ERROR);
            $error = '位置id不存在';
            return false;
        }

        if(!isset($game_id) || empty($game_id))
        {
            \Yii::getLogger()->log('游戏id不存在',Logger::LEVEL_ERROR);
            $error = '游戏id不存在';
            return false;
        }

        $rst = \Yii::$app->cache->get('niuniu_game_info_'.$living_id);
        if($rst === false)
        {
            $error = '游戏信息处理异常3';
            return false;
        }
        $rst = json_decode($rst,true);
        //\Yii::getLogger()->log('抢庄家前缓存  ===:'.var_export($rst,true),Logger::LEVEL_ERROR);
        $banker_poker = [];
        foreach($rst['poker_info'] as $reat)              //查找出庄家
        {
            if($reat['seat_num'] == $seat_num)
            {
                $banker_poker = [
                    'seat_num' => $reat['seat_num'],
                    'poker_result' => $reat['poker_result'],
                    'win_num' => 0,
                    'is_banker' => 2,
                ];
                break;
            }

        }
        $poker_result = [];
        foreach($rst['poker_info'] as &$poker)
        {
            if($poker['seat_num'] == $banker_poker['seat_num'])
            {
//                排除庄家
                continue;
            }
            if(($banker_poker['poker_result'] == 0) || ($banker_poker['poker_result'] >= $poker['poker_result'] && $poker['poker_result'] != 0)) {
                $banker_poker['win_num']++;
                $poker['win_num'] = '-1';
            }
            else
            {
                $banker_poker['win_num']--;
                $poker['win_num'] = '1';
            }

        }
        foreach($rst['poker_info'] as &$res)
        {
            if($res['seat_num'] == $banker_poker['seat_num'])
            {
                $res['win_num'] = strval($banker_poker['win_num']);
                $res['is_win'] = strval($banker_poker['win_num']) >0 ? 2 : 1;
                $res['is_banker'] = 2;
                $res['multiple'] = $multiple;
                $res['chip_num'] = $res['win_num']*$res['multiple']*$res['base_num'];  //输赢的筹码
            }
            else
            {
                $res['is_win'] = $res['win_num'] >0 ? 2 : 1;
                $res['is_banker'] = 1;
                $res['chip_num'] = $res['win_num']*$res['multiple']*$res['base_num'];  //输赢的筹码
            }
            $poker_result[] = [
                'seat_num' => $res['seat_num'],
                'poker_result' => $res['poker_result'],
                'user_id' => $res['user_id'],
                'base_num' => $res['base_num'],
                'multiple' => $res['multiple'],
                'win_num' => $res['win_num'],
                'is_banker' => $res['is_banker']
            ];
        }
        //\Yii::getLogger()->log('抢庄家后缓存  ===:'.var_export($rst,true),Logger::LEVEL_ERROR);
        $cache_info = \Yii::$app->cache->set('niuniu_game_info_'.$living_id,json_encode($rst),5*60);
        if(!$cache_info)
        {
            \Yii::getLogger()->log('座位列表缓存更新失败   $cache_info==:'.var_export($rst,true),Logger::LEVEL_ERROR);
            $error = '获取座位列表信息失败';
            return false;
        }
        return $poker_result;
    }

}