<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/3
 * Time: 16:53
 */

namespace frontend\zhiboapi\v1;


use frontend\business\ApiCommon;
use frontend\business\ClientInfoUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 获取超管常用语列表协议 hbh
 * Class ZhiBoGetAdminWarning
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetAdminWarning implements IApiExcute
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

        $status = $LoginInfo['client_type'];
        if($status != 2)
        {
            \Yii::error(var_export($LoginInfo,true));
            //$error = '不是超管用户，获取警告语列表信息失败';
            return false;
        }
        $user_id = $LoginInfo['user_id'];
        $warning_list = ClientInfoUtil::GetAdminWarningList($user_id);

        //\Yii::getLogger()->log('admin_list_v2::'.var_export($warning_list,true),Logger::LEVEL_ERROR);
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $warning_list;
        return true;
    }

    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no'];
        $fieldLabels = ['唯一号'];
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