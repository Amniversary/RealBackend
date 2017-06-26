<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-26
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v3;

use frontend\business\ApiCommon;
use frontend\business\LivingUtil;
use frontend\zhiboapi\IApiExcute;


/**
 * Class 弹幕
 * @package frontend\zhiboapi\v3
 */
class ZhiBoDanmaku implements IApiExcute
{
    /**
     * TODO: 检查参数合法性
     * @param string $error
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['living_id'];
        $fieldLabels = ['直播id'];
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

    public function excute_action($dataProtocal, &$rstData,&$error = '', $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal, $error))
        {
            return false;
        }
        $deviceNo = '';
        $uniqueNo= '';
        $registerType='';
        $deviceType='';
        if(!ApiCommon::GetBaseInfoFromProtocol($dataProtocal, $deviceNo, $uniqueNo,$registerType,$deviceType,$error))
        {
            return false;
        }
        $loginInfo = null;
        if(!ApiCommon::GetLoginInfo($uniqueNo,$loginInfo, $error))
        {
            return false;
        }
        $user_id  = $loginInfo['user_id'];
        $passParams = $dataProtocal['data'];
        unset($passParams['unique_no']);
        unset($passParams['register_type']);
        $passParams['device_type'] = $deviceType;
        $passParams['device_no'] = $deviceNo;

        if(!LivingUtil::Danmaku($passParams,$user_id,$error))
        {
            return false;
        }

        $rstData['has_data'] ='0';
        $rstData['data_type'] = 'string';
        $rstData['data'] = '';
        return true;
    }
}


