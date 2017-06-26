<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-06-28
 * Time: 16:36
 */

namespace frontend\zhiboapi\v1;

use frontend\business\ApiCommon;
use frontend\business\ClientUtil;
use frontend\business\LivingUtil;
use frontend\business\LivingNewUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;
use common\components\ClearCacheHelper;

use \common\components\tenxunlivingsdk\TimRestApi;
/**
 * 禁用用户
 * @package frontend\zhiboapi\v3
 */
class ZhiBoBanClient implements IApiExcute
{
    /**
     * 检查参数合法性
     * @param $dataProtocal
     * @param string $error
     * @return bool
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['client_id','ban_content'];
        $field_names = ['被封号的用户ID','封号理由'];
        for($i=0;$i<count($fields);$i++)
        {
            if(!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]]))
            {
                $error = $field_names[$i].'不能为空';
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
        if(($dataProtocal['device_type'] == 2) && ($dataProtocal['app_version_inner'] == 20))  //兼容IOS 的20版本传送参数错误，传的为living_id
        {
            $living_info = LivingUtil::GetLivingById($dataProtocal['data']['client_id']);
            $ban_user = ClientUtil::GetClientById($living_info->living_master_id);
        }
        /* else
        {
            $ban_user = ClientUtil::GetClientById($dataProtocal['data']['client_id']);
        }*/

        if( !$ban_user )
        {
            $ban_user = ClientUtil::GetClientById($dataProtocal['data']['client_id']);
        }

        if(!$ban_user)
        {
            $error = '被封号用户不存在';
            //\Yii::getLogger()->log('ban======>:'.var_export($dataProtocal, true),Logger::LEVEL_ERROR);
            return false;
        }
        if($ban_user->status == 0)
        {
            $error = '用户已经被封号了';
            return false;
        }

        if($ban_user->client_type == 2)
        {
            $error = '超级管理员不能被禁用';
            return false;
        }
        $admin_user = ApiCommon::GetLoginInfo($dataProtocal['data']['unique_no'],$OutUserInfo,$error);//根据unique_no 获取用户client_id
        if(!$admin_user)
        {
            $error = '用户不存在';
            return false;
        }

        if($OutUserInfo['client_type'] != 2)
        {
            \Yii::getLogger()->log('login_info=:'.var_export($OutUserInfo,true),Logger::LEVEL_ERROR);
            $error = '不是超级管理员，不能禁用用户';
            return false;
        }
        $ban_user->status = 0;
        $ban_user->city = (string)$ban_user->city;
        $ban_user->remark1 = $dataProtocal['data']['ban_content'];
        if(!ClientUtil::SaveClient($ban_user,$error))
        {
            return false;
        }


        $data = [
            'client_no'=>$ban_user->client_no,
            'nick_name'=>$ban_user->nick_name,
            'manage_id'=>$OutUserInfo['user_id'],
            'manage_name'=>$OutUserInfo['nick_name'],
            'operate_type'=>'1',
            'management_type'=>'1',
            'create_time'=>date('Y-m-d H:i:s'),
            'remark1' => $dataProtocal['data']['ban_content']
        ];
        //增加禁用日志
        if(!ClientUtil::CreateCloseUserByLog($data,$error))
        {
            return false;
        }

        $living_info = LivingUtil::GetClientLivingInfoByLivingMasterId($dataProtocal['data']['client_id']);
        //如果正在直播，强制结束直播
        if($living_info['status'] == 2)
        {
            ClearCacheHelper::ClearHotLivingDataCache();
            $finishInfo = null;
            if(!LivingNewUtil::SetBanClientFinishLivingToStopLiving($living_info['living_id'],$finishInfo,$living_info['living_master_id'],$living_info['other_id'],$outInfo,$error))
            {
                return false;
            }
        }

        return true;
    }
}