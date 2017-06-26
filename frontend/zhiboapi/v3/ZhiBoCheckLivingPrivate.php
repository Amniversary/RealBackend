<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-21
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v3;

use frontend\business\ApiCommon;
use frontend\business\LivingPrivateUtil;
use frontend\zhiboapi\IApiExcute;


/**
 * 验证私密直播
 * Class ZhiBoCheckLivingPrivate
 * @package frontend\zhiboapi\v3
 */
class ZhiBoCheckLivingPrivate implements IApiExcute
{
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        if(!ApiCommon::GetLoginInfo($dataProtocal['data']['unique_no'],$LoginInfo,$error))
        {
            return false;
        }
        if(!LivingPrivateUtil::CheckPrivatePassword($dataProtocal['data']['living_id'],$LoginInfo['user_id'],$error))
        {
            return false;
        }

        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
        $rstData['data'] = [];

        return true;
    }
}