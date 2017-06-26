<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/14
 * Time: 16:31
 */

namespace frontend\zhiboapi\v3\niuniu;


use frontend\business\ApiCommon;
use frontend\business\GoldsAccountUtil;
use frontend\business\JobUtil;
use frontend\business\NiuNiuGameGrabSeatUtil;
use frontend\business\NiuNiuGameUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 玩家下注协议接口 Hbh
 * Class ZhiBoGamePlayerBet
 * @package frontend\zhiboapi\v2\niuniu
 */
class ZhiBoGamePlayerBet implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal, $error))
        {
            return false;
        }

        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no, $LoginInfo, $error))
        {
            return false;
        }

        $game_id = $dataProtocal['data']['game_id'];
        $base_num = $dataProtocal['data']['base_num'];
        $game_info = NiuNiuGameUtil::GetNiuNiuGameById($game_id);
        if(!isset($game_info))
        {
            $error = '游戏记录不存在';
            return false;
        }
        $game_seat_info = NiuNiuGameGrabSeatUtil::GetGameSeatByGameIdAndUserIdInfo($game_id,$LoginInfo['user_id']);
        if($game_seat_info['is_robot'] == 1)
        {
            $gold_balance = GoldsAccountUtil::GetGoldsAccountModleByUserId($LoginInfo['user_id']);
            if($game_info->living_master_id == $LoginInfo['user_id'])
            {
                if($gold_balance->gold_account_balance < $base_num * 3)
                {
                    $error = '游戏币余额不足，无法下注';
                    \Yii::getLogger()->log($error.': gold_money:'.$gold_balance->gold_account_balance.';  base_num:'.$base_num * 3,Logger::LEVEL_ERROR);
                    return false;
                }
            }
            else
            {
                if($gold_balance->gold_account_balance < $base_num)
                {
                    $error = '游戏币余额不足，无法下注';
                    \Yii::getLogger()->log($error.': gold_money:'.$gold_balance->gold_account_balance.';  base_num:'.$base_num,Logger::LEVEL_ERROR);
                    return false;
                }
            }
        }

        if($game_info->game_status != 2)
        {
            $error = '下注时间已过现在不能下注';
            \Yii::getLogger()->log($error.': game_id :'.$game_id.';  game_status:'.$game_info->game_status,Logger::LEVEL_ERROR);
            return false;
        }
        $data = [
            'key_word'=>'niuniu_game_grab_bet',
            'game_id'=>$game_id,
            'base_num'=>$base_num,
            'user_id'=>$LoginInfo['user_id'],
            'living_id'=>$game_info['living_id'],
            'game_seat_info'=>$game_seat_info,
        ];

        $jobSever = 'NiuNiuGameGrabSeatBeanstalk';
        if(!JobUtil::AddCustomJob($jobSever, 'niuniu_game_grab_bet', $data, $error))
        {
            return false;
        }



        $rstData['has_data'] = '0';
        $rstData['data_type'] = 'string';
        $rstData['data'] = '';
        return true;
    }

    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no','game_id','base_num'];
        $fieldLabels = ['唯一号','游戏id','押注数'];
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