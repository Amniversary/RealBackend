<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/23
 * Time: 11:04
 */

namespace frontend\zhiboapi\v1;


use frontend\business\ApiCommon;
use frontend\business\RedPacketsUtil;
use frontend\zhiboapi\IApiExcute;

/**
 * 生成红包协议  hbh
 * Class ZhiBoGenerateRedPackets
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGenerateRedPackets implements  IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no','red_money','red_num','red_type'];
        $fieldLabels = ['唯一标识','红包金额','红包个数','红包类型'];
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

    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }

        $uniqueNo = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }

        $data = [
            'user_id' => $LoginInfo['user_id'],
            'red_money'=>$dataProtocal['data']['red_money'],
            'red_num'=>$dataProtocal['data']['red_num'],
            'red_type'=>$dataProtocal['data']['red_type'],
            'device_type'=>$dataProtocal['device_type']
        ];

        if(!RedPacketsUtil::CreateRedPacket($data,$out,$error))
        {
            return false;
        }

        $rstData['has_data'] ='1';
        $rstData['data_type'] = 'json';
        $rstData['data'] = ['red_packet_id'=>$out['gu_id']];
        return true;
    }
} 