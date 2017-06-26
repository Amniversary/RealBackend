<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/27
 * Time: 15:28
 */

namespace frontend\zhiboapi\v3;


use frontend\business\ApiCommon;
use frontend\business\ClientUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 修改个人信息
 * Class ZhiBoCompleteClient
 * @package frontend\zhiboapi\v3
 */
class ZhiBoCompleteClient implements IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no','register_type'];
        $fieldLabels = ['唯一号','登录类型'];
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

    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
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

        $fileds_ok=[
            'nick_name',
            'pic',
            'sex',
            'age',
            'city',
            'sign_name',
            'getui_id'
        ];

        $data = [];
        foreach($dataProtocal['data'] as $k => $v)
        {
            if(in_array($k,$fileds_ok) && $v != '')
            {
                $data[$k]=$v;
            }
        }

        if(!empty($data))
        {
            if(!ClientUtil::UpdateUser($data,$uniqueNo,$registerType,$error))
            {
                return false;
            }
        }

        $rstData['has_data'] = '0';
        $rstData['data_type'] = 'json';
        $rstData['data'] = [];
        return true;
    }
} 