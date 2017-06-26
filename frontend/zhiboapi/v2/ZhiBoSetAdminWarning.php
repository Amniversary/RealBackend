<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/4
 * Time: 17:06
 */

namespace frontend\zhiboapi\v2;


use frontend\business\ApiCommon;
use frontend\business\ClientInfoUtil;
use frontend\zhiboapi\IApiExcute;

class ZhiBoSetAdminWarning implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }

        $uniqueNo = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($uniqueNo, $LoginInfo, $error))
        {
            return false;
        }

        $user_id = $LoginInfo['user_id'];
        $content = $dataProtocal['data']['content'];
        if(!ClientInfoUtil::SetAdminWarning($user_id,$content,$error))
        {
            return false;
        }

        $rstData['has_data'] = '0';
        $rstData['data_type'] = 'string';
        $rstData['data'] = '';
        return true;
    }

    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no','content'];
        $fieldLabels = ['唯一号','常用语内容'];
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