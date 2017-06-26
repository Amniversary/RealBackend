<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/25
 * Time: 16:33
 */

namespace frontend\zhiboapi\v2\waistcoat;


use frontend\business\ApiCommon;
use frontend\business\ClientUtil;
use frontend\business\LivingUtil;


class PublicLivingRoomSearch implements IExcute
{
    function action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
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
        $appid  =  CreateFilterCoat::GetFilterCoat( $dataProtocal['app_id'] );
        $is_police = ($LoginInfo['client_type'] == '2' ? 1 : 0);
        $room_list = ClientUtil::LivingRoomSearchByAppId($appid,$room_no,$LoginInfo['user_id']);
        $rst = $room_list;
        $str_data = LivingUtil::GetLivingConf($room_list['living_type']);
        $free_num = intval(array_count_values($str_data)[0]);

        $room_list['is_police'] = $is_police;

        if($room_list['guess_num'] == -1)
        {
            $room_list['free_num'] = $free_num;
            $room_list['guess_num'] = count($str_data);
            $room_list['guess_money'] = $str_data[0];
        }
        else
        {
            if($room_list['living_type'] == 3)
            {
                $room_list['guess_money'] = $str_data[$room_list['guess_num']];
            }
            elseif($room_list['living_type'] == 4)
            {
                $room_list['guess_money'] = intval($str_data[$room_list['guess_num']]*$room_list['tickets']);
            }
            $room_list['guess_num'] = count($str_data)-$room_list['guess_num'] ;  //剩余竞猜的次数
        }
        if(empty($rst))
        {
            $rst = [];
        }
        else
        {
            $rst = $room_list;
        }
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