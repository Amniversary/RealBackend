<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/15
 * Time: 11:00
 */

namespace frontend\zhiboapi\v3;

use frontend\business\ApiCommon;
use frontend\business\LivingHotUtil;
use frontend\business\LivingUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

class ZhiBoGetPushLivingInfo implements IApiExcute
{

    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        $uniqueNo = $dataProtocal['data']['unique_no'];
        $living_master_id = $dataProtocal['data']['user_id'];
        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }
        $rst = LivingHotUtil::GetOneHotLivingInfo($living_master_id,$LoginInfo['user_id']);
        if(empty($rst))
        {
            $rst = [];
        }
        else
        {
            $is_police = ($LoginInfo['client_type'] == '2' ? 1 : 0);
            if($rst['living_type'] == 5)
            {
                $guess_conf_array = LivingUtil::GetLivingConf(3);
            }
            else
            {
                $guess_conf_array = LivingUtil::GetLivingConf($rst['living_type']);
            }
            $guess_living_conf_no = intval(array_count_values($guess_conf_array)[0]);
            if($rst['guess_num'] == -1)
            {
                $rst['over_guess_num'] = strval($guess_living_conf_no);  //免费的次数
                $rst['guess_num'] = strval(count($guess_conf_array));  //剩余的次数
                $rst['flowers_num'] = strval($guess_conf_array[0]);
            }
            else
            {
                if(($rst['living_type'] == 3) || ($rst['living_type'] == 5))
                {
                    $rst['flowers_num'] = strval($guess_conf_array[$rst['guess_num']]);
                }
                elseif($rst['living_type'] == 4)
                {
                    $rst['flowers_num'] = strval(ceil($guess_conf_array[$rst['guess_num']]*$rst['tickets']));
                }
                $rst['guess_num'] = strval(count($guess_conf_array)-$rst['guess_num']) ;  //剩余竞猜的次数
            }

            $rst['is_police'] = strval($is_police);
            $rst['flowers_num'] = empty($rst['flowers_num'])?'0':$rst['flowers_num'];
        }
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $rst;
        return true;
    }
}