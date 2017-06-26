<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/12
 * Time: 9:41
 */

namespace frontend\zhiboapi\v3;


use frontend\business\ApiCommon;
use frontend\business\ClientUtil;
use frontend\business\DynamicUtil;
use frontend\business\JobUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\DynamicClickSaveByTrans;
use frontend\zhiboapi\IApiExcute;

/**
 * 动态圈点赞协议
 * Class ZhiBoDynamicClickLick
 * @package frontend\zhiboapi\v3
 */
class ZhiBoDynamicClickLick implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if (!$this->check_param_ok($dataProtocal, $error))
        {
            return false;
        }

        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no, $LoginInfo, $error))
        {
            return false;
        }

        $dynamic_id = $dataProtocal['data']['dynamic_id'];
        $Dynamic = DynamicUtil::GetDynamicById($dynamic_id);
        if(!isset($Dynamic))
        {
            $error = '动态记录不存在';
            return false;
        }

        $dynamic_like = \Yii::$app->cache->get('get_dynamic_like_'.$Dynamic->user_id.'_'.$dynamic_id.'_'.$LoginInfo['user_id']);
        if($dynamic_like !== false)
        {
            $error = '您已经点过赞了哟~';
            return false;
        }
        $client = ClientUtil::GetClientById($LoginInfo['user_id']);
        $jobSever = 'dynamicBeanstalk';
        $data = [
            'key_word'=>'dynamic_click',
            'dynamic_id'=>$dynamic_id,
            'user_id'=>$Dynamic->user_id,
            'pic'=>$client->pic,
            'content'=>'',
            'dynamic_pic'=>$Dynamic->pic,
            'click_user_id'=>$LoginInfo['user_id'],
            'nick_name'=>$LoginInfo['nick_name'],
            'create_time'=>date('Y-m-d H:i:s'),
            'type'=>'3',
        ];

        if(!JobUtil::AddCustomJob($jobSever,'dynamic_click',$data,$error))
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

        $fields = ['unique_no','dynamic_id'];
        $fieldLabels = ['唯一号','动态id'];
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