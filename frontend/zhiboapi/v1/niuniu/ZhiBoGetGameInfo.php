<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/13
 * Time: 15:22
 */

namespace frontend\zhiboapi\v3\niuniu;


use frontend\business\ApiCommon;
use frontend\business\NiuNiuGameUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 获取牛牛游戏信息协议 Hbh
 * Class ZhiBoGetGameInfo
 * @package frontend\zhiboapi\v2\niuniu
 */
class ZhiBoGetGameInfo implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        //\Yii::getLogger()->log('dataPrio:'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
        if (!$this->check_param_ok($dataProtocal, $error))
        {
           return false;
        }
        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no, $LoginInfo, $error))
        {
            return false;
        }
        $living_id = $dataProtocal['data']['living_id'];

        $cache = \Yii::$app->cache->get('niuniu_game_'.$living_id);
        $cache = json_decode($cache,true);
        $game_info = \Yii::$app->cache->get('niuniu_game_info_'.$living_id);
        $poker_info = json_decode($game_info,true);

        $data = [];
        if(!empty($poker_info))
        {
            $poker_info['sync_time'] = intval($cache['sync_time']);
            $data = $poker_info;
        }
        else
        {
            //用于处理当APP请求关闭游戏时，未收到IM消息缓存被删除，再获取游戏信息接口返回空数据
            if(($cache['game_status'] == 5) || ($cache['game_status'] == 6))
            {

                $now_seat_all = NiuNiuGameUtil::GetNiuNiuGameInfo($cache['game_id']);
                if(!empty($now_seat_all))
                {
                    $outAll = [
                        'game_id' => $cache['record_id'],
                        'game_status' => $cache['game_status'],
                        'sync_time' => intval($cache['sync_time'])
                    ];
                    foreach($now_seat_all as $seat)
                    {
                        $poker = json_decode($seat['poker_info'], true);
                        $outAll['poker_info'][$seat['seat_num']] = [
                            'user_id' => $seat['user_id'],
                            'pic' => $seat['pic'],
                            'nick_name' => $seat['nick_name'],
                            'seat_status' => $seat['seat_status'],
                            'is_banker' => $seat['is_banker'],
                            'is_robot' => $seat['is_robot'],
                            'is_living_master' => $seat['is_living_master'],
                            'is_win' => ($seat['win_num']>0?2:1),
                            'seat_num'=>$seat['seat_num'],
                            'chip_num'=>$seat['chip_num'],
                            'base_num' => $seat['base_num'],
                            'multiple' => $seat['multiple'],
                            'poker_result' => $seat['poker_result'],
                            'poker1' => $poker['poker1'],
                            'poker2' => $poker['poker2'],
                            'poker3' => $poker['poker3'],
                            'poker4' => $poker['poker4'],
                            'poker5' => $poker['poker5'],
                            'win_num' => $seat['win_num']
                        ];
                    }
                    $data = $outAll;
                }
                \Yii::getLogger()->log('重新获取游戏结果:'.var_export($data,true),Logger::LEVEL_ERROR);
            }
        }
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarrry';
        $rstData['data'] = $data;
        return true;
    }


    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no','living_id'];
        $fieldLabels = ['唯一号','直播间id'];
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }

        return true;
    }

    /*$data = [
                'game_id'=>$cache['game_id'],
                'game_status'=>$cache['game_status'],
                'sync_time'=>intval($cache['sync_time']),
            ];

            foreach($poker_info as $info)
            {
                $poker = json_decode($info['poker_info'],true);
                $data['poker_info'][$info['seat_num']] = [
                    'user_id'=>$info['user_id'],
                    'pic'=>$info['pic'],
                    'nick_name'=>$info['nick_name'],
                    'seat_status'=>$info['seat_status'],
                    'is_banker'=>$info['is_banker'],
                    'is_robot'=>$info['is_robot'],
                    'is_living_master'=>$info['is_living_master'],
                    'is_win'=>(($info['win_num'] > 0)? 2 : 1),
                    'seat_num'=>$info['seat_num'],
                    'chip_num'=>$info['chip_num'],
                    'base_num'=>$info['base_num'],
                    'multiple'=>$info['multiple'],
                    'poker_result'=>$info['poker_result'],
                    'poker1'=>$poker['poker1'],
                    'poker2'=>$poker['poker2'],
                    'poker3'=>$poker['poker3'],
                    'poker4'=>$poker['poker4'],
                    'poker5'=>$poker['poker5'],
                ];
                if($info['win_num'] == 0)
                {
                    $data['poker_info'][$info['seat_num']]['is_win'] = 0;
                }
            }*/
} 