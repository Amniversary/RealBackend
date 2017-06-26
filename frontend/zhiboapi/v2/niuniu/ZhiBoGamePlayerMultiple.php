<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/14
 * Time: 16:32
 */

namespace frontend\zhiboapi\v2\niuniu;


use frontend\business\ApiCommon;
use frontend\business\GoldsAccountUtil;
use frontend\business\JobUtil;
use frontend\business\NiuNiuGameGrabSeatUtil;
use frontend\business\NiuNiuGameUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

class ZhiBoGamePlayerMultiple implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        //\Yii::getLogger()->log('multiple:'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
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
        $multiple = $dataProtocal['data']['multiple'];
        $game_seat_info = NiuNiuGameGrabSeatUtil::GetGameSeatByGameIdAndUserIdInfo($game_id,$LoginInfo['user_id']);
        $gold_balance = GoldsAccountUtil::GetGoldsAccountModleByUserId($LoginInfo['user_id']);
        if($gold_balance->gold_account_balance < ($game_seat_info['base_num'] * $multiple))
        {
            $error = '游戏币余额不足，无法选择该倍数';
            return false;
        }
        $game_info = NiuNiuGameUtil::GetNiuNiuGameById($game_id);

        if($game_seat_info['is_banker'] == 2)
        {
            $error = '庄家不允许叫倍';
            return false;
        }
        if(!in_array($multiple,[1,3,7,11,25]))
        {
            $error = '叫倍倍数不正确';
            return false;
        }
        if(!isset($game_info))
        {
            $error = '游戏记录不存在';
            return false;
        }

        if($game_info->game_status != 4)
        {
            $error = '叫倍时间已过现在不能叫倍';
            \Yii::getLogger()->log($error.': game_id:'.$game_id.';  game_status:'.$game_info->game_status,Logger::LEVEL_ERROR);
            return false;
        }

        $dataAll = [
            'key_word'=>'niuniu_game_grab_multiple',
            'game_id'=>$game_id,
            'multiple'=>$multiple,
            'living_id'=>$game_info->living_id,
            'user_id'=>$LoginInfo['user_id'],
            'game_seat_info'=>$game_seat_info,
        ];
        $jobSever = 'NiuNiuGameGrabSeatBeanstalk';
        if(!JobUtil::AddCustomJob($jobSever, 'niuniu_game_grab_multiple', $dataAll, $error))
        {
            return false;
        }


        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = '';
        return true;
    }

    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no','game_id','multiple'];
        $fieldLabels = ['唯一号','游戏id','倍数'];
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