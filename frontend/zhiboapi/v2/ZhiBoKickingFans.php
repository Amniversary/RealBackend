<?php
/**
 * Created by PhpStorm.
 * User: Zff
 * Date: 2016/9/10
 * Time: 15:00
 */

namespace frontend\zhiboapi\v2;

use common\models\FansGroupMember;
use frontend\business\ApiCommon;
use frontend\business\FansGroupUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

class ZhiBoKickingFans implements IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['group_id', 'user_id'];
        $fieldLabels = ['粉丝群ID', '用户ID'];
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

        /*$unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no, $sysLoginInfo, $error))
        {
            return false;
        }
        //获得用户的id
        $user_id = $sysLoginInfo['user_id'];
        $group_id = $dataProtocal['data']['group_id'];
        $member = FansGroupMember::findOne(['user_id'=>$user_id, 'group_id'=>$group_id]);
        if($member->group_member_type==0){
            //普通粉丝
            $error = '只有群主和管理员才能踢人哦';
            return false;
        }*/
        $group_id = $dataProtocal['data']['group_id'];
        $user_id = $dataProtocal['data']['user_id'];
        $group_info = FansGroupUtil::KickingFans($group_id, $user_id, $error);

        if(!$group_info)
        {
            return false;
        }

        $rstData['has_data']='1';
        $rstData['data_type']='string';
        $rstData['data']  = '踢人成功';
        return true;
    }
} 