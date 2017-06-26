<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/3
 * Time: 9:58
 */
namespace frontend\controllers\MblivingActions;
use backend\components\ExitUtil;
use frontend\business\ClientInfoUtil;
use frontend\business\ClientUtil;
use frontend\business\LivingUtil;
use yii\base\Action;
use yii\log\Logger;

class MbUserLevelAction extends Action
{
    public function run($user_id,$unique_no,$rand_str,$time,$sign)
    {

        $params['user_id'] = $user_id;
        $params['unique_no'] = $unique_no;
        $params['rand_str'] = $rand_str;
        $params['time'] = $time;
        $rst = ['code'=>'1','msg'=>''];
        if(!isset($user_id))
        {
            $rst['msg'] = '用户id不能为空';
            echo json_encode($rst);
            exit;
        }
        //\Yii::getLogger()->log('get_level_info:'.var_export($params,true),Logger::LEVEL_ERROR);
        $sourceSign = ClientUtil::GetClientSign($params);
        //\Yii::getLogger()->log('sign:'.$sign,Logger::LEVEL_ERROR);
        if($sourceSign !== $sign)
        {
            $rst['msg'] = '签名信息错误';
            echo json_encode($rst);
            exit;
        }

        if(!LivingUtil::GetUserLevelInfo($user_id,$LevelInfo,$error))
        {
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }

        if(!isset($LevelInfo))
        {
            $error = '用户相关等级信息未找到';
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }
        $rst['code'] = '0';
        $rst['msg'] = $LevelInfo;
        echo json_encode($rst);
        exit;
    }
} 