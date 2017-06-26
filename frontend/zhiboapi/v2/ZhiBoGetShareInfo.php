<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 15:59
 */

namespace frontend\zhiboapi\v2;


use frontend\business\ApiCommon;
use frontend\business\JobUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 获取分享统计信息协议 hbh
 * Class ZhiBoGetShareInfo
 * @package frontend\zhiboapi\v2
 */
class ZhiBoGetShareInfo implements IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['share_type','client_type','user_id'];
        $fieldLabels = ['分享类型','分享用户类型','直播id'];
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

    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        //\Yii::getLogger()->log('datainfofofofo:'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }
        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no,$LoginInfo,$error))
        {
            return false;
        }

        $living_master_id = $dataProtocal['data']['user_id'];
        $user_id = $LoginInfo['user_id'];
        $client_type = $dataProtocal['data']['client_type'];
        $share_type = $dataProtocal['data']['share_type'];
        if(!in_array($share_type,[1,2,3,4,5]))
        {
            $error = '分享类型不存在';
            \Yii::getLogger()->log($error.' :'.$share_type,Logger::LEVEL_ERROR);
            return false;
        }
        if(!in_array($client_type,[1,2]))
        {
            $error = '分享用户类型不存在';
            \Yii::getLogger()->log($error.' :'.$client_type,Logger::LEVEL_ERROR);
            return false;
        }
        if($client_type == 1)   //主播分享
        {
            if($living_master_id != $user_id)
            {
                $error = '主播对应id不正确';
                return false;
            }
        }

        if($client_type == 2)
        {
            if($living_master_id == $user_id)
            {
                $client_type = 1;
            }
        }

        $data = [
            'key_word'=>'share_living_info',
            'living_master_id'=>$living_master_id,
            'share_type'=>$share_type,
            'client_type'=>$client_type,
        ];

        if(!JobUtil::AddShareLivingJob('living_share_info',$data,$error))
        {
            return false;
        }

        $rstData['has_data']= '0';
        $rstData['data_type'] = 'json';
        $rstData['data'] = '';
        return true;
    }
} 