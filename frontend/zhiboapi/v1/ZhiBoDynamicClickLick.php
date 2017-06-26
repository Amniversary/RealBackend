<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/12
 * Time: 9:41
 */

namespace frontend\zhiboapi\v1;


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
        //\Yii::error('data clicklike:'.var_export($dataProtocal,true));
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

        $data = [
            'key_word'=>'send_dynamic_ry_im',
            'dynamic_id'=>$dynamic_id,
            'dynamic_user_id'=>$Dynamic->user_id,
            'user_id'=>$LoginInfo['user_id'],
            'to_user_id'=>$Dynamic->user_id,
            'content'=>'',
            'dynamic_pic'=>$Dynamic->pic,
            'nick_name'=>$LoginInfo['nick_name'],
            'pic'=>(isset($client->icon_pic) ? $client->icon_pic : $client->pic),
            'create_time'=>date('Y-m-d H:i:s'),
            'type'=>'3',
        ];

        \Yii::$app->cache->set('get_dynamic_like_'.$Dynamic->user_id.'_'.$dynamic_id.'_'.$LoginInfo['user_id'],1);

        if(!DynamicUtil::UpdateDynamicClick($data['dynamic_id'], $error)) {
            return false;
        }
        if(!JobUtil::AddImJob('tencent_im', $data, $error)) {
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