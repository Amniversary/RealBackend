<?php
/**
 * Created by PhpStorm.
 * 直播间操作
 * User: wld
 * Date: 2017/2/18
 * Time: 10:27
 */

namespace frontend\controllers\MblivingActions;
use frontend\business\ApiCommon;
use frontend\business\AttentionUtil;
use frontend\business\ClientUtil;
use frontend\business\LivingHotUtil;
use frontend\business\LivingUtil;
use yii\base\Action;
use yii\log\Logger;

class MblLvingOperationAction extends Action
{
    public function run()
    {
        $rst = ['code'=>'1','msg'=>''];
        $data = \Yii::$app->request->post();
        $living_id = $data['living_id'];
        $type = $data['type'];

        if(!isset($living_id))
        {
            $rst['msg'] = '未获取到房间号:user_id';
            echo json_encode($rst);
            exit;
        }
        if(!isset($type))
        {
            $rst['msg'] = '未获取到操作直播间类型';
            echo json_encode($rst);
            exit;
        }

        //type ： 1 进行关闭直播间处理
        if($type == 1)
        {

        }


        //type ： 2 进行警告直播间处理
        if($type == 2)
        {

        }


        //type ： 3 进行封号处理
        if($type == 3)
        {

        }


        $living_master_info = LivingUtil::GetUserInfo($living_id);




        $rst['code'] = '0';
        $rst['msg'] = $test;
        echo  json_encode($rst);
        exit;
    }
}




