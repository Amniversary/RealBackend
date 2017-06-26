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

class ZhiBoGroupMemberPrivilege implements IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['group_id', 'user_id', 'group_member_type'];
        $fieldLabels = ['粉丝群ID', '用户id', '权限类型'];
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]])) {
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



        if(!FansGroupUtil::GroupMemberPrivilege($dataProtocal, $error))
        {
            return false;
        }

        $rstData['has_data']='1';
        $rstData['data_type']='string';
        $rstData['data']  = '权限修改成功';
        return true;
    }
} 