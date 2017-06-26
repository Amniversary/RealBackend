<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/24
 * Time: 11:05
 */

namespace frontend\zhiboapi\v3;


use frontend\business\ApiCommon;
use frontend\business\ClientUtil;
use frontend\testcase\IApiExcute;
use Pili\Api;

class ZhiBoFansGroupSearch implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal, $error))
        {
            return false;
        }
        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no,$LoginInfo, $error))
        {
            return false;
        }

        $user_id = $LoginInfo['user_id'];
        $key_word = $dataProtocal['data']['key_word'];
        $page_no = $dataProtocal['data']['page_no'];
        $page_size = $dataProtocal['data']['page_size'];

        $fansGroupList = ClientUtil::FansGroupSearch($key_word,$page_no,$page_size,$user_id);

        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $fansGroupList;
        return true;
    }

    private function check_param_ok($dataProtocal,&$error='')
    {
        if(!isset($dataProtocal['data']['key_word']))
        {
            $error = '关键字，不能为空';
            return false;
        }
        $fields = ['unique_no','page_no','page_size'];
        $fieldLabels = ['唯一id','页码','每页记录数'];
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }
        if(intval($dataProtocal['data']['page_no']) <= 0)
        {
            $error = '页码数不正确';
            return false;
        }
        if(intval($dataProtocal['data']['page_size']) <= 0)
        {
            $error = '页记录数不正确';
            return false;
        }
        return true;
    }
} 