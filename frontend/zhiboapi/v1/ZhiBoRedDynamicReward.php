<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/10
 * Time: 16:22
 */

namespace frontend\zhiboapi\v1;


use frontend\business\ApiCommon;
use frontend\business\BalanceUtil;
use frontend\business\DynamicUtil;
use frontend\business\DynamicNewUtil;
use frontend\zhiboapi\IApiExcute;

/**
 * 红包动态打赏协议 hbh
 * Class ZhiBoRedDynamicReward
 * @package frontend\zhiboapi\v3
 */
class ZhiBoRedDynamicReward implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal, $error))
        {
            return false;
        }

        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no, $LoginInfo, $error))
        {
            return false;
        }
        $device_type = $dataProtocal['device_type'];
        $dynamic_id = $dataProtocal['data']['dynamic_id'];
        $Dynamic = DynamicUtil::GetDynamicById($dynamic_id);
        if(!isset($Dynamic))
        {
            $error = '动态记录不存在';
            return false;
        }

        if(!DynamicNewUtil::CreateRedDynamic($Dynamic,$LoginInfo,$device_type,$error))
        {
            return false;
        }

        return true;
    }


    private function check_param_ok($dataProtocal,&$error='')
    {

        $fields = ['unique_no','dynamic_id'];
        $fieldLabels = ['唯一号','动态id'];
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