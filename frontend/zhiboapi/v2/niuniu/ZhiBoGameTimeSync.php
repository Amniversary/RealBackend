<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/13
 * Time: 13:57
 */

namespace frontend\zhiboapi\v2\niuniu;


use common\components\GameRebotsHelper;
use frontend\business\ApiCommon;
use frontend\business\JobUtil;
use frontend\business\NiuNiuGameGrabSeatUtil;
use frontend\business\NiuNiuGameUtil;
use frontend\business\RobotUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 游戏时间同步协议 Hbh
 * Class ZhiBoGameTimeSync
 * @package frontend\zhiboapi\v2\niuniu
 */
class ZhiBoGameTimeSync implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        //\Yii::getLogger()->log('dataProto:'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
        if (!$this->check_param_ok($dataProtocal, $error))
        {
            return false;
        }
        $unique_no = $dataProtocal['data']['unique_no'];
        if (!ApiCommon::GetLoginInfo($unique_no, $LoginInfo, $error))
        {
            return false;
        }
        $game_id = $dataProtocal['data']['game_id'];
        $game_status = $dataProtocal['data']['game_status'];
        $sync_time = $dataProtocal['data']['sync_time'];
        $living_id = $dataProtocal['data']['living_id'];
        $data = [
            'game_id'=>intval($game_id),
            'sync_time'=>intval($sync_time),
            'game_status'=>intval($game_status),
        ];
        $rst = json_encode($data);

        \Yii::$app->cache->set('niuniu_game_'.$living_id, $rst,300); // 5 * 60
        \Yii::getLogger()->log('sync_time:'.var_export($data,true),Logger::LEVEL_ERROR);
        if($game_status == 2)
        {
            if($sync_time <= 6)
            {
                $seat_info = NiuNiuGameGrabSeatUtil::GetGameSeatInfo($game_id);
                $user_All = [];
                foreach($seat_info as $info)
                {
                    if($info['seat_status'] == 1)
                    {
                        $robot_all =  GameRebotsHelper::GetRebots();
                        //\Yii::getLogger()->log('robot_all:'.var_export($robot_all,true),Logger::LEVEL_ERROR);
                        $user_All[] = $robot_all[0]['client_id'];
                    }
                }

                //\Yii::getLogger()->log('robot_info:'.var_export($seat_info,true),Logger::LEVEL_ERROR);
                $grab_seat_data = [
                    'game_id' => $game_id,
                    'user_id' => $user_All,
                    'living_id' => $living_id,
                    'is_robot' => 2,
                ];
                if(!JobUtil::AddCustomJob('NiuNiuGameGrabSeatBeanstalk','niuniugame_grab_seat',$grab_seat_data,$error))
                {
                    \Yii::getLogger()->log('ZhiBoGameGrabSeats    $error====:'.$error,Logger::LEVEL_ERROR);
                    return false;
                }
            }
        }
        $rstData['has_data'] = '0';
        $rstData['data_type'] = 'string';
        $rstData['data'] = '';
        return true;
    }


    private function check_param_ok($dataProtocal,&$error='')
    {
        if(!isset($dataProtocal['data']['sync_time']))
        {
            $error = '同步时间，不能为空';
            return false;
        }
        $fields = ['unique_no','game_id','game_status','living_id'];
        $fieldLabels = ['唯一号','游戏id','游戏状态','直播间id'];
        $len = count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }

        return true;
    }
} 