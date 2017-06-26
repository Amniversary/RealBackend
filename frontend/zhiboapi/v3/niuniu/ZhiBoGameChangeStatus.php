<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-23
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v3\niuniu;

use common\components\PhpLock;
use frontend\business\ApiCommon;
use frontend\business\JobUtil;
use frontend\business\LivingUtil;
use frontend\business\NiuNiuGameUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;


/**
 * 改变游戏状态协议接口
 * Class ZhiBoGameChangeStatus
 * @package frontend\zhiboapi\v2\niuniu
 */
class ZhiBoGameChangeStatus implements IApiExcute
{

    /**
     * 改变游戏状态协议
     * @param $dataProtocal
     * @param $rstData
     * @param $error
     * @param array $extendData
     * @return bool
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        //$test_time1 = microtime(true);
        //\Yii::getLogger()->log('datapro:'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
        $error = '';
        $game_status = $dataProtocal['data']['game_status'];
        $game_id = $dataProtocal['data']['game_id'];
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
        $niuniu_game_info = NiuNiuGameUtil::GetNiuNiuGameById($game_id);
        if(!$niuniu_game_info)
        {
            \Yii::getLogger()->log('牛牛游戏信息不存在  game_id==:'.$game_id,Logger::LEVEL_ERROR);
            $error = '游戏信息不存在';
            return false;
        }
        if(!in_array($game_status,[1,2,3,4,5,6]))
        {
            $error = '游戏状态不正确';
            return false;
        }
        if($niuniu_game_info->game_status != $game_status)
        {
            $niuniu_game_info->game_status = $game_status;
            if(!NiuNiuGameUtil::SaveNiuNiuGame($niuniu_game_info,$error))
            {
                return false;
            }
            if($game_status < 5)
            {
                $rst = \Yii::$app->cache->get('niuniu_game_info_'.$niuniu_game_info->living_id);
                $rst_info = json_decode($rst,true);
                $rst_info['game_status'] = $game_status;
                $data = json_encode($rst_info);
                $cache = \Yii::$app->cache->set('niuniu_game_info_'.$niuniu_game_info->living_id,$data,5*60);
                if(!$cache)
                {
                    $error = '状态缓存失败';
                    return false;
                }
            }

            if($game_status == 5)  //结束游戏，自动开始下局游戏，机器人清除并回收，计算主播打赏金额
            {

                $status_data = [
                    'game_id' => $game_id,
                    'game_status' => $game_status,
                    'device_type' => $dataProtocal['device_type'],
                ];

                if(!JobUtil::AddCustomJob('NiuNiuGameGrabSeatBeanstalk','niuniugame_set_robot',$status_data,$error))
                {
                    \Yii::getLogger()->log('ZhiBoGameGrabBankert    $error====:'.$error,Logger::LEVEL_ERROR);
                    return false;
                }
            }

            if($game_status == 6)  //主播结束游戏，不再自动开始下局游戏
            {
                $status_data = [
                    'game_id' => $game_id,
                    'game_status' => $game_status,
                    'device_type' => $dataProtocal['device_type'],
                ];
                if(!JobUtil::AddCustomJob('NiuNiuGameGrabSeatBeanstalk','niuniugame_set_robot',$status_data,$error))
                {
                    \Yii::getLogger()->log('ZhiBoGameGrabBankert    $error====:'.$error,Logger::LEVEL_ERROR);
                    return false;
                }
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