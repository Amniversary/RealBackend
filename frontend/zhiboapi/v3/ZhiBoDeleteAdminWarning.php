<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/4
 * Time: 17:36
 */

namespace frontend\zhiboapi\v3;


use common\models\CommonWords;
use frontend\business\ApiCommon;
use frontend\business\ClientInfoUtil;
use frontend\testcase\IApiExcute;

/**
 * 删除超管常用语
 * Class ZhiBoDeleteAdminWarning
 * @package frontend\zhiboapi\v3
 */
class ZhiBoDeleteAdminWarning implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }

        $uniqueNo = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }
        $user_id = $LoginInfo['user_id'];
        $cid = $dataProtocal['data']['cid'];
        if(!ClientInfoUtil::DeleteAdminWarning($user_id,$cid,$error))
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
        $fields = ['unique_no','cid'];
        $fieldLabels = ['唯一号','内容 ID'];
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