<?php
/**
 * Created by PhpStorm.
 * User: Zff
 * Date: 2016/9/10
 * Time: 15:00
 */

namespace frontend\zhiboapi\v1;

use frontend\business\ApiCommon;
use frontend\business\FansGroupUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

class ZhiBoGetFansGroupInfo implements IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['group_id'];
        $fieldLabels = ['粉丝群ID'];
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

    public function excute_action($dataProtocal, &$rstData, &$error, $extendData= array())
    {
        $error = '';
//        if(!$this->check_param_ok($dataProtocal,$error))
//        {
//            return false;
//        }

        $group_id = $dataProtocal['data']['group_id'];
        $unique_no = $dataProtocal['data']['unique_no'];
        $tx_group_id = $dataProtocal['data']['tx_group_id'];
        if(!ApiCommon::GetLoginInfo($unique_no, $sysLoginInfo, $error))
        {
            return false;
        }
        //获取请求人的user_id
        $user_id = $sysLoginInfo['user_id'];

        $group_info = FansGroupUtil::GetFansGroupInfoByGroupID($group_id, $tx_group_id, $user_id, $error);

        if(!$group_info)
        {
            $error = '获取群信息失败1';
            return false;
        }

        if($group_info['member_number'] < 1)
        {
            $error = '获取群信息失败2';
            return false;
        }

        $rstData['has_data']='1';
        $rstData['data_type']='string';
        $rstData['data']  = $group_info;
        return true;
    }
} 