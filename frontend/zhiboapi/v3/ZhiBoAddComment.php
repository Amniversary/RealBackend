<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/10
 * Time: 9:58
 */

namespace frontend\zhiboapi\v3;


use frontend\business\ApiCommon;
use frontend\business\ClientUtil;
use frontend\business\DynamicUtil;
use frontend\business\JobUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\AddCommentSaveByTrans;
use frontend\zhiboapi\IApiExcute;

/**
 * 蜜圈评论协议接口
 * Class ZhiBoAddComment
 * @package frontend\zhiboapi\v3
 */
class ZhiBoAddComment implements IApiExcute
{
    /**
     * @param $dataProtocal
     * @param $rstData
     * @param $error
     * @param array $extendData
     * @return bool
     */
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }

        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no,$LoginInfo,$error))
        {
            return false;
        }
        $dynamic_id = $dataProtocal['data']['dynamic_id'];
        $user_id = '';
        //不为空 则是评论回复
        if(!empty($dataProtocal['data']['user_id']))
        {
            $user_id = $dataProtocal['data']['user_id'];
            $Client = ClientUtil::GetClientById($user_id);
            if (!isset($Client))
            {
                $error = '回复的用户id不存在';
                return false;
            }
        }
        $Login_client = ClientUtil::GetClientById($LoginInfo['user_id']);
        $dynamic = DynamicUtil::GetDynamicById($dynamic_id);
        if(!isset($dynamic))
        {
            $error = '动态记录不存在';
            return false;
        }

        $data = [
            'key_word'=>'set_comment',
            'dynamic_id'=>$dynamic_id,
            'dynamic_user_id'=>$dynamic->user_id,
            'user_id'=>$LoginInfo['user_id'],
            'to_user_id'=>$user_id,
            'content'=>$dataProtocal['data']['content'],
            'status'=>1,
            'dynamic_pic'=>$dynamic->pic,
            'nick_name'=>$LoginInfo['nick_name'],
            'pic'=>(isset($Login_client->icon_pic) ? $Login_client->icon_pic : $Login_client->pic),
            'type'=>'2',
        ];

        $jobSever = 'dynamicBeanstalk';
        if(!JobUtil::AddCustomJob($jobSever,'dynamic_comment',$data,$error))
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
        $fields = ['unique_no','dynamic_id','content'];
        $fieldLabels = ['唯一号','动态id','评论内容'];
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