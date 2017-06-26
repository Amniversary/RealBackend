<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/14
 * Time: 11:32
 */

namespace frontend\zhiboapi\v1;


use frontend\business\ApiCommon;
use frontend\business\RongCloud\UserUtil;
use frontend\business\RongCloudUtil;
use frontend\zhiboapi\IApiExcute;

class ZhiBoGetToken implements IApiExcute
{

    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!isset($dataProtocal['data']['unique_no']) || empty($dataProtocal['data']['unique_no']))
        {
            $error = 'unique_no，不能为空';
            return false;
        }
        $uniqueNo = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($uniqueNo, $Login, $error))
        {
            return false;
        }

        if(!UserUtil::getUserToken($Login['user_id'],$data,$error))
        {
            return false;
        }

        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'string';
        $rstData['data'] = $data['token'];
        return true;
    }
} 