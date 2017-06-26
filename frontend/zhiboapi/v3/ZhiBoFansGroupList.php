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

/**
 * 获取粉丝群
 * Class ZhiBoFansGroupList
 * @package frontend\zhiboapi\v3
 */
class ZhiBoFansGroupList implements IApiExcute
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
        /*if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }*/
        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no, $sysLoginInfo, $error))
        {
            return false;
        }
        //获得用户的id
        $user_id = $sysLoginInfo['user_id'];
        //$user_id = $dataProtocal['data']['user_id'];

        $list = FansGroupUtil::GetFansGroupListByUserID($user_id, $error);

        $rstData['has_data']='1';
        $rstData['data_type']='jsonarray';
        $rstData['data']  = $list;
        return true;
    }
} 