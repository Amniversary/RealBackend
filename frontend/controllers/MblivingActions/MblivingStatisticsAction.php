<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/29
 * Time: 13:55
 */

namespace frontend\controllers\MblivingActions;


use frontend\business\ClientUtil;
use frontend\business\LivingUtil;
use yii\base\Action;
use yii\log\Logger;

class MblivingStatisticsAction extends Action
{
    public function run()
    {
        $rst = ['code'=>'1','msg'=>''];
        $user_id = \Yii::$app->session['living_user_id'];
        //\Yii::getLogger()->log('user_id:'.$$user_id,Logger::LEVEL_ERROR);
        if(!isset($user_id))
        {
            $rst['msg'] = '您还未绑定蜜播账号，请先绑定蜜播账号！';
            echo json_encode($rst);
            exit;
        }

        $client = ClientUtil::GetClientById($user_id);
        if(!isset($client))
        {
            $rst['msg'] = '用户信息不存在!';
            echo json_encode($rst);
            exit;
        }

        $union_id = \Yii::$app->session['time_unionid'];
        //$union_id = '2.00PAXnvDLpIHcC87e360b94askY9_E';
        //\Yii::getLogger()->log('union_id:'.$union_id,Logger::LEVEL_ERROR);
        if(!isset($union_id))
        {
            $rst['msg'] = '系统信息错误!';
            \Yii::getLogger()->log($rst['msg'].': '.$union_id.'union_id为空',Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        $otherInfo = ClientUtil::GetClientOtherInfo($union_id);
        if(!isset($otherInfo))
        {
            $rst['msg'] = '用户未绑定微信蜜播账号，请到蜜播App绑定微信账号!';
            echo json_encode($rst);
            exit;
        }
        if(!LivingUtil::LivingTimeStatistics($client->client_id,$out,$error))
        {
            $rst['msg'] = $error;
            \Yii::getLogger()->log('time::'.var_export($out,true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        $data = [
            'client_no' => $client->client_no,
            'pic'=>$client->pic,
            'nick_name'=>$client->nick_name,
            'living_time'=>$out,
        ];
        //\Yii::getLogger()->log('timeor:'.var_export($data,true),Logger::LEVEL_ERROR);

        $rst['code'] = '0';
        $rst['msg'] = $data;
        echo json_encode($rst);
    }
} 