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
use frontend\business\NiuNiuGameGrabBankerUtil;
use frontend\business\NiuNiuGameGrabSeatUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;


/**
 * 叫外围获取协议接口
 * Class ZhiBoGameGrabPeriphery
 * @package frontend\zhiboapi\v2\niuniu
 */
class ZhiBoGameGrabPeriphery implements IApiExcute
{

    /**
     * 叫外围获取协议接口
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
        $seat_num = $dataProtocal['data']['seat_num'];
        $base_num = $dataProtocal['data']['base_num'];
        $living_id = $dataProtocal['data']['living_id'];
        $multiple = 1;//$dataProtocal['data']['multiple'];  //后面修改为外围用户没有倍数
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
        $niuniu_game_info = NiuNiuGameGrabSeatUtil::GetGameSeatByGameIdAndSeatNumInfo($game_id,$seat_num);
        $my_account_balance_info = GoldsAccountUtil::GetGoldsAccountInfoByUserId($LoginInfo['user_id']);
        if(!$niuniu_game_info)
        {
            \Yii::getLogger()->log('用户位置信息不存在  $niuniu_game_info==:'.var_export($niuniu_game_info,true),Logger::LEVEL_ERROR);
            $error = '用户位置信息不存在';
            return false;
        }
        $rst = \Yii::$app->cache->get('niuniu_game_info_'.$living_id);
        $cache = json_decode($rst,true);
        if($cache['game_status'] != 4)   //4为外围状态
        {
            \Yii::getLogger()->log('游戏状态不正确  game_status==:'.$cache['game_status'],Logger::LEVEL_ERROR);
            $error = '游戏状态不正确'.$cache['game_status'];
            return false;
        }

        $periphery_info = NiuNiuGameGrabSeatUtil::GetGamePeripheryInfoByGameIdAndUserId($game_id,$LoginInfo['user_id']);
        if(!empty($periphery_info))
        {
            $error = '已经压过外围了';
            return false;
        }

//        if(!in_array($base_num,[100,300,1000,10000]))
//        {
//            $error = '底注不正确';
//            return false;
//        }

//        if(!in_array($multiple,[1,2,3,5,10]))
//        {
//            $error = '倍数不正确';
//            return false;
//        }

        if($niuniu_game_info['is_banker'] == 2)
        {
            if($my_account_balance_info['gold_account_balance'] < ($base_num*$multiple*3))             //压庄家至少3倍
            {
                $error = '金币余额不足,不能压外围';
                return false;
            }
        }
        else
        {
            if($my_account_balance_info['gold_account_balance'] < ($base_num*$multiple))
            {
                $error = '金币余额不足,不能压外围';
                return false;
            }
        }

        $out_info = NiuNiuGameGrabSeatUtil::GetReturnGrabPerlphery($seat_num,$base_num,$multiple,$living_id,$error);
        if(!$out_info)
        {
            $error = '座位结果信息不存在';
            return false;
        }
        $banker_data = [
            'living_id' => $living_id,
            'game_id' => $game_id,
            'device_type' => $dataProtocal['device_type'],
            'seat_num' => $seat_num,
            'base_num' => $base_num,
            'multiple' => $multiple,
            'user_id' => $LoginInfo['user_id'],
            'is_win' => $out_info['is_win'],
            'win_num' => $out_info['win_num'],
            'win_money_num' => $out_info['win_money_num'],
            'gold_account_id' => $my_account_balance_info['gold_account_id']
        ];

        if(!JobUtil::AddCustomJob('NiuNiuGameGrabSeatBeanstalk','niuniugame_grab_periphery',$banker_data,$error))
        {
            \Yii::getLogger()->log('ZhiBoGameGrabPeriphery    $error====:'.$error,Logger::LEVEL_ERROR);
        }

        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $out_info;
        //$test_time2 = microtime(true);
        //$alltime = $test_time2-$test_time1;
        return true;
    }
}