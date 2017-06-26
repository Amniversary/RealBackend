<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-23
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v2\niuniu;

use frontend\business\ApiCommon;
use frontend\business\JobUtil;
use frontend\business\NiuNiuGameGrabBankerUtil;
use frontend\business\NiuNiuGameGrabSeatUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;


/**
 * 抢庄结果获取协议接口
 * Class ZhiBoGameGrabBanker
 * @package frontend\zhiboapi\v2\niuniu
 */
class ZhiBoGameGrabBanker implements IApiExcute
{

    /**
     * 抢庄结果获取协议接口
     * @param $dataProtocal
     * @param $rstData
     * @param $error
     * @param array $extendData
     * @return bool
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        //$test_time1 = microtime(true);
        $game_id = $dataProtocal['data']['game_id'];
        $multiple = $dataProtocal['data']['multiple'];
        $seat_num = $dataProtocal['data']['seat_num'];
        if(!isset($game_id) || empty($game_id))
        {
            $error = '游戏id不能为空';
            return false;
        }
        if(!isset($seat_num) || empty($seat_num))
        {
            $error = '座位号不能为空';
            return false;
        }
        $uniqueNo = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }

        $niuniu_game_info = NiuNiuGameGrabSeatUtil::GetGameByGameIdAndSeatNumInfo($game_id,$seat_num);
        if(!$niuniu_game_info)
        {
            \Yii::getLogger()->log('用户位置信息不存在  $niuniu_game_info==:'.var_export($niuniu_game_info,true),Logger::LEVEL_ERROR);
            $error = '用户位置信息不存在';
            return false;
        }

        if($niuniu_game_info['game_status'] != 3)
        {
            $error = '游戏状态不正确'.$niuniu_game_info['game_status'];
            \Yii::getLogger()->log($error.'  status===:'.var_export($niuniu_game_info,true),Logger::LEVEL_ERROR);
            return false;
        }

        if(!in_array($multiple,[1,2,6,10,20]))
        {
            $error = '倍数不正确';
            return false;
        }

        if($niuniu_game_info['gold_account_balance'] < ($niuniu_game_info['base_num']*$multiple*3))
        {
            $error = '金币余额不足,不能抢庄';
            return false;
        }

        $info = NiuNiuGameGrabSeatUtil::CheckGameGrabBanker($game_id);
        if(!empty($info))
        {
            $error = '庄家已经存在了   ';
            \Yii::getLogger()->log($error.'   $info===:'.var_export($info,true),Logger::LEVEL_ERROR);
            return false;
        }

        $win_info = NiuNiuGameGrabBankerUtil::DoGrabBanker($dataProtocal['data']['living_id'],$niuniu_game_info['seat_num'],$game_id,$multiple,$error);  //比较大小
        if(!$win_info)
        {
            return false;
        }
        $banker_data = [
            'living_id' => $dataProtocal['data']['living_id'],
            'game_id' => $game_id,
            'user_id' => $niuniu_game_info['user_id'],
            'device_type' => $dataProtocal['device_type'],
        ];

        if(!JobUtil::AddCustomJob('NiuNiuGameGrabSeatBeanstalk','niuniugame_grab_banker',$banker_data,$error))
        {
            \Yii::getLogger()->log('ZhiBoGameGrabBankert    $error====:'.$error,Logger::LEVEL_ERROR);
            return false;
        }
        //\Yii::getLogger()->log('win_info===:'.var_export($win_info,true),Logger::LEVEL_ERROR);
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = [
            'game_status' => $niuniu_game_info['game_status'],
            'seat_info' => $win_info
        ];

        //$test_time2 = microtime(true);
        //$alltime = $test_time2-$test_time1;
        return true;
    }
}