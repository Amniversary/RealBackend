<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-23
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v2\niuniu;

use frontend\business\ApiCommon;
use frontend\business\GoldsAccountUtil;
use frontend\business\JobUtil;
use frontend\business\NiuNiuGameGrabSeatUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;


/**
 * 游戏抢座位协议接口
 * Class ZhiBoGameChangeStatus
 * @package frontend\zhiboapi\v2\niuniu
 */
class ZhiBoGameGrabSeats implements IApiExcute
{

    /**
     * 游戏抢座位协议接口
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
        $living_id = $dataProtocal['data']['living_id'];
        if(!isset($game_id) || empty($game_id))
        {
            $error = '游戏id不能为空';
            return false;
        }
        $uniqueNo = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }
        $rst = \Yii::$app->cache->get('niuniu_game_info_'.$living_id);
        if($rst === false)
        {
            $error = '抢座位缓存不存在 $living_id===:'.$living_id;
            return false;
        }

        $cache = json_decode($rst,true);
        if($cache['game_status'] != 2)   //2为抢座状态
        {
            \Yii::getLogger()->log('游戏状态不正确  game_status==:'.$cache['game_status'],Logger::LEVEL_ERROR);
            $error = '游戏状态不正确';
            return false;
        }
        $seat_info = NiuNiuGameGrabSeatUtil::GetGameSeatInfoByGameIdAndUserId($game_id,$LoginInfo['user_id']);
        if(!empty($seat_info))
        {
            $error = '已经抢到座位了';
            return false;
        }
        $user_golds_account = GoldsAccountUtil::GetGoldsAccountModleByUserId($LoginInfo['user_id']);
        if(empty($user_golds_account))
        {
            $error = '用户金币信息不存在';
            return false;
        }
        if($user_golds_account->gold_account_balance < 100)  //币余额最少于100，不能上座
        {
            $error = '金币余额不足，不能抢座';
            return false;
        }

        $grab_seat_data = [
            'game_id' => $game_id,
            'user_id' => [$LoginInfo['user_id']],
            'living_id' => $living_id,
            'is_robot' => 1,
            'device_type' => $dataProtocal['device_type'],
        ];
        if(!JobUtil::AddCustomJob('NiuNiuGameGrabSeatBeanstalk','niuniugame_grab_seat',$grab_seat_data,$error))
        {
            \Yii::getLogger()->log('ZhiBoGameGrabSeats    $error====:'.$error,Logger::LEVEL_ERROR);
            return false;
        }


        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = [];
        //$test_time2 = microtime(true);
        //$alltime = $test_time2-$test_time1;
        return true;
    }
}