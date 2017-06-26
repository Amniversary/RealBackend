<?php
/**
 * Created by PhpStorm.
 * User: Zff
 * Date: 2016/9/10
 * Time: 15:00
 */

namespace frontend\zhiboapi\v3;

use frontend\business\ApiCommon;
use frontend\business\FansGroupUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

class ZhiBoGroupMemberList implements IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['group_id', 'page'];
        $fieldLabels = ['粉丝群ID', '当前页page'];
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
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }
        $group_id = $dataProtocal['data']['group_id'];
        $page = $dataProtocal['data']['page'];
        $page_size = $dataProtocal['data']['page_size'];
        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no, $sysLoginInfo, $error))
        {
            return false;
        }
        $user_id = $sysLoginInfo['user_id'];
        $list = FansGroupUtil::GetGroupMemberList($group_id, $user_id, $page, $page_size);

        $rstData['has_data']='1';
        $rstData['data_type']='jsonarray';
        $rstData['data']  = $list;
        return true;
    }
} 