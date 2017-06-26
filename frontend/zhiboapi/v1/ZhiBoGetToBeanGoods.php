<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/26
 * Time: 14:10
 */

namespace frontend\zhiboapi\v1;


use frontend\business\ApiCommon;
use frontend\business\ClientInfoUtil;
use frontend\zhiboapi\IApiExcute;

class ZhiBoGetToBeanGoods implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $uniqueNo = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($uniqueNo,$sysLoginInfo,$error))
        {
            return false;
        }
        if(!ClientInfoUtil::GetBeanGoods($outInfo,$error))
        {
            return false;
        }
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $outInfo;

        return true;
    }
} 