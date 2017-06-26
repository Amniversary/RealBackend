<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-23
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v3\niuniu;

use common\components\GameRebotsHelper;
use common\components\PhpLock;
use frontend\business\ApiCommon;
use frontend\business\ClientUtil;
use frontend\business\JobUtil;
use frontend\business\LivingUtil;
use frontend\business\NiuNiuGameGrabSeatUtil;
use frontend\business\NiuNiuGameUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;


/**
 * 离开座位协议接口
 * Class ZhiBoGameLeaveSeat
 * @package frontend\zhiboapi\v2\niuniu
 */
class ZhiBoGameLeaveSeat implements IApiExcute
{

    /**
     * 离开座位协议接口
     * @param $dataProtocal
     * @param $rstData
     * @param $error
     * @param array $extendData
     * @return bool
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        //$test_time1 = microtime(true);
        $error = '';
        $game_id = $dataProtocal['data']['game_id'];
        $is_normal = $dataProtocal['data']['is_normal'];
        $living_id = $dataProtocal['data']['living_id'];
        if(!isset($game_id) || empty($game_id))
        {
            $error = '游戏id不能为空';
            return false;
        }
        if(!isset($living_id) || empty($living_id))
        {
            $error = '直播间id不能为空';
            return false;
        }
        $uniqueNo = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }

        if(!in_array($is_normal,[1,2]))
        {
            $error = '状态错误';
            return false;
        }

        $seat_info = NiuNiuGameGrabSeatUtil::GetGameSeatByGameIdAndUserIdInfo($game_id,$LoginInfo['user_id']);
        if(!$seat_info)
        {
            $error = '用户位置信息不存在';
            return false;
        }
        $time_info = \Yii::$app->cache->get('niuniu_game_'.$living_id);
        if($is_normal == 1)    //正常离开，时间小于6s 在同步时间接口里设置机器人
        {
            if($time_info['sync_time'] > 6)
            {
                $grab_seat_data = [
                    'game_id' => $game_id,
                    'user_id' => $LoginInfo['user_id'],
                    'living_id' => $living_id,
                    'record_id'=> $seat_info['record_id'],
                ];
                if(!JobUtil::AddCustomJob('NiuNiuGameGrabSeatBeanstalk','niuniugame_leave_seat',$grab_seat_data,$error))
                {
                    \Yii::getLogger()->log('ZhiBoGameGrabSeats    $error====:'.$error,Logger::LEVEL_ERROR);
                    return false;
                }
            }
        }
        else
        {
            $seat_obj = NiuNiuGameGrabSeatUtil::GetGameSeatInfoById($seat_info['record_id']);
            $seat_obj->is_normal = 2;
            if(!NiuNiuGameGrabSeatUtil::UpdateSeatInfo($seat_obj,$error))
            {
                $error = '用户离开座位信息修改失败';
                return false;
            }
        }
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = [];
        //$test_time2 = microtime(true);
        //$alltime = $test_time2-$test_time1;
        return true;
    }
}