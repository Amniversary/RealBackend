<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/24
 * Time: 14:04
 */

namespace frontend\zhiboapi\v1;


use frontend\business\ApiCommon;
use frontend\business\ClientUtil;
use frontend\business\LivingUtil;
use frontend\testcase\IApiExcute;
use yii\log\Logger;

class ZhiBoLivingRoomSearch implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        \Yii::getLogger()->log('room_list_aaaa :'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
        if(!$this->check_param_ok($dataProtocal, $error))
        {
            return false;
        }
        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no, $LoginInfo ,$error))
        {
            return false;
        }

        $room_no = $dataProtocal['data']['room_no'];
        $is_police = ($LoginInfo['client_type'] == '2' ? 1 : 0);
        $room_list = ClientUtil::LivingRoomSearch($room_no,$LoginInfo['user_id']);
        $rst = $room_list;
        $str_data = LivingUtil::GetLivingConf($room_list['living_type']);
        \Yii::getLogger()->log('room_listaaaasdads :'.var_export($str_data,true),Logger::LEVEL_ERROR);
        $free_num = strval(array_count_values($str_data)[0]);

        $room_list['is_police'] = strval($is_police);

        if($room_list['guess_num'] == -1)
        {
            $room_list['free_num'] = strval($free_num);
            $room_list['guess_num'] = strval(count($str_data));
            $room_list['guess_money'] = strval($str_data[0]);
        }
        else
        {
            if($room_list['living_type'] == 3)
            {
                $room_list['guess_money'] = strval($str_data[$room_list['guess_num']]);
            }
            elseif($room_list['living_type'] == 4)
            {
                $room_list['guess_money'] = strval(ceil($str_data[$room_list['guess_num']] * $room_list['tickets']));
            }
            $room_list['guess_num'] = strval(count($str_data) - $room_list['guess_num']) ;  //剩余竞猜的次数
        }

        if(empty($rst))
        {
            $rst = [];
        }
        else
        {
            $rst = $room_list;
        }
        \Yii::getLogger()->log('room_list :'.var_export($rst,true),Logger::LEVEL_ERROR);
        $rstData['has_date'] = '1';
        $rstData['data_type'] ='jsonarray';
        $rstData['data'] = $rst;
        return true;
    }

    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no','room_no'];
        $fieldLabels = ['唯一id','房间号'];
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
} 