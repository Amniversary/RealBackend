<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/26
 * Time: 15:31
 */

namespace frontend\controllers\MblivingActions;


use frontend\business\ActivityUtil;
use frontend\business\ApiCommon;
use frontend\business\ChatUtil;
use frontend\business\ClientUtil;
use frontend\business\JobUtil;
use yii\base\Action;
use yii\log\Logger;

class MblivingAttentionAction extends Action
{
    public function run($unique_no,$rand_str,$time,$sign)
    {
        $rst = ['code'=>'1','msg'=>''];

        $params['unique_no'] = $unique_no;
        $params['rand_str'] = $rand_str;
        $params['time'] = $time;

        $sourceSign = ActivityUtil::GetActivitySign($params);
        if($sourceSign !== $sign)
        {
            $rst['msg'] = '签名信息错误';
            \Yii::getLogger()->log('sourceSing:'.$sourceSign.' sign:'.$sign,Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        $post = \Yii::$app->request->post('living_master_id');

        if(!ApiCommon::GetLoginInfo($unique_no,$LoginInfo,$error))
        {
            $rst['msg'] = $error;
            \Yii::getLogger()->log('GetLogin:'.var_export($rst,true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        if(!isset($post) || empty($post))
        {
            $rst['msg'] = '请求参数错误!';
            echo json_encode($rst);
            exit;
        }

        $living_master_id = $post;
        $user_id = $LoginInfo['user_id'];
        //\Yii::getLogger()->log('user_id :'.$living_master_id. 'friend_user_id :'.$user_id,Logger::LEVEL_ERROR);
        if(!ChatUtil::Attention($living_master_id,$user_id,$error))
        {
            $rst['msg'] = $error;
            \Yii::getLogger()->log('attention:'.var_export($rst,true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        //加入异步任务处理
        $data=[
            'user_id'=>$user_id,
            'attention_id'=>$living_master_id,
            'op_type'=>'attention'
        ];

        if(!JobUtil::AddAttentionJob('user_attention',$data,$error))
        {
            \Yii::getLogger()->log('job save error:'.$error,Logger::LEVEL_ERROR);
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }

        $rst['code'] = '0';
        //\Yii::getLogger()->log('Rst:'.var_export($rst,true),Logger::LEVEL_ERROR);
        echo json_encode($rst);
    }
} 