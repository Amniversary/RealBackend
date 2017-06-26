<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/25
 * Time: 9:32
 */

namespace frontend\zhiboapi\v2;


use frontend\business\ApiCommon;
use frontend\business\ClientGoodsUtil;
use frontend\business\ClientUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

class ZhiBoUpdateClient implements IApiExcute{

    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no','register_type','nick_name','pic','sex'];
        $fieldLabels = ['唯一号','登录类型','昵称信息','头像信息','性别信息'];
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

        if(!empty($dataProtocal['data']['age']))
        {
            $data['age'] = $dataProtocal['data']['age'];
        }
        if(!empty($dataProtocal['data']['city']))
        {
            $data['city'] = $dataProtocal['data']['city'];
        }
        if(!empty($dataProtocal['data']['sign_name']))
        {
            $data['sign_name'] = $dataProtocal['data']['sign_name'];
        }


        if(!empty($dataProtocal['data']['inviter_id']))
        {
//            \Yii::getLogger()->log('data_inviter_id='.var_export($dataProtocal['data']['inviter_id'],true),Logger::LEVEL_ERROR);
            $inviter_info = ClientUtil::GetClientNo($dataProtocal['data']['inviter_id']);
            if(!empty($inviter_info))
            {
//                \Yii::getLogger()->log('inviter_info='.var_export($inviter_info,true),Logger::LEVEL_ERROR);
                ClientGoodsUtil::AddClientFlower($dataProtocal['data']['inviter_id']);
//                \Yii::getLogger()->log('邀请人加鲜花成功',Logger::LEVEL_ERROR);
                $user_info = ClientUtil::GetClientByUniqueNo($uniqueNo);
                ClientGoodsUtil::AddClientFlower($user_info['client_no']);
            }
            else
            {
                $error= '邀请人信息不存在';
                return false;
            }
        }


        $data['nick_name'] = $dataProtocal['data']['nick_name'];
        $data['sex'] = $dataProtocal['data']['sex'];
        $data['pic'] = $dataProtocal['data']['pic'];

        if(!ClientUtil::UpdateUser($data,$uniqueNo,$registerType,$error))
        {
            return false;
        }
        $rstData['has_data'] = '0';
        $rstData['data_type'] = 'json';
        $rstData['data'] = [];
        return true;
    }
} 