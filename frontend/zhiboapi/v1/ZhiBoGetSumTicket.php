<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-26
 * Time: 上午11:30
 */

namespace frontend\zhiboapi\v1;

use frontend\business\ApiCommon;
use frontend\business\ClientUtil;
use frontend\business\LivingUtil;
use frontend\zhiboapi\IApiExcute;
use yii\db\Query;


/**
 * Class 获取用户总票数
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetSumTicket implements IApiExcute
{

    /**
     * 获取用户总票数
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        $uniqueNo = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }

        $rst = LivingUtil::GetSumTicket($LoginInfo);

        if(!isset($rst))
        {
            $rst = [];
        }

        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
        $rstData['data']= $rst;

        return true;
    }
}


