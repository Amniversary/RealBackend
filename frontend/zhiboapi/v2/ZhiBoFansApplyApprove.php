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

class ZhiBoFansApplyApprove implements IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['user_id', 'group_id', 'apply_status'];
        $fieldLabels = ['同意入群用户的ID', '粉丝群ID', '群主审核的结果'];
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
        //\Yii::getLogger()->log($error.' zff-zpprove:'.var_export($dataProtocal['data'],true),Logger::LEVEL_ERROR);
        $unique_no = $dataProtocal['data']['unique_no'];
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
            $error = '只有群主或管理员才能添加人哦';
            return false;
        }
        $user_id = $dataProtocal['data']['user_id'];
        $group_id = $dataProtocal['data']['group_id'];
        $apply_status = $dataProtocal['data']['apply_status'];



        if(!FansGroupUtil::FansApplyApprove($group_id, $user_id, $apply_status, $error))
        {
            $error = '申请审核失败';
            return false;
        }

        $rstData['has_data']='1';
        $rstData['data_type']='string';
        $rstData['data']  = '';
        return true;
    }
} 