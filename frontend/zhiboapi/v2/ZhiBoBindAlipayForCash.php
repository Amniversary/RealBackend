<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/26
 * Time: 19:21
 */

namespace frontend\zhiboapi\v2;


use frontend\business\ApiCommon;
use frontend\business\ClientInfoUtil;
use frontend\zhiboapi\IApiExcute;

class ZhiBoBindAlipayForCash implements IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['alipay_no','real_name','identity_no'];
        $fieldLabels = ['支付宝账号信息','真实姓名','身份证号'];
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
        $registerType = $dataProtocal['data']['register_type'];
        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }
        $LoginInfo['alipay'] = $dataProtocal['data']['alipay_no'];
        $LoginInfo['real_name'] = $dataProtocal['data']['real_name'];
        $LoginInfo['identity_no'] = $dataProtocal['data']['identity_no'];

        if(!ClientInfoUtil::GetBindAlipay($LoginInfo,$registerType,$error))
        {
            return false;
        }
        
        $rstData['has_data'] = '0';
        $rstData['data_type'] = 'string';
        $rstData['data'] = [];

        return true;
    }
} 