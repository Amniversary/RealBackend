<?php

namespace backend\controllers\ClientActions;


use frontend\business\ClientRobotUtil;
use yii\base\Action;

class SetRobotNoAction extends Action
{
    public function run($user_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($user_id))
        {
            $rst['message'] = '用户id不能为空';
            echo json_encode($rst);
            exit;
        }

        //当用户信息不存在时，将用户加入到表  mb_client_robotinfo
        $ss = ClientRobotUtil::GetClientRobot($user_id);
        if(!isset($ss))
        {
            ClientRobotUtil::AddRobotInfo($user_id);
        }


        $client = ClientRobotUtil::GetClientRobot($user_id);
        if(!isset($client))
        {
            $rst['message'] = '用户记录不存在';
            echo json_encode($rst);
            exit;
        }


        $hasEdit = \Yii::$app->request->post('hasEditable');
        if(!isset($hasEdit))
        {
            $rst['message'] = 'hasEditable参数为空';
            echo json_encode($rst);
            exit;
        }
        if(empty($hasEdit))
        {
            $rst['message'] = '';
            echo json_encode($rst);
            exit;
        }
        $editIndex = \Yii::$app->request->post('editableIndex');
        if(!isset($editIndex))
        {
            $rst['message'] = 'editableIndex参数为空';
            echo json_encode($rst);
            exit;
        }


        $create_robot_no = \Yii::$app->request->post('create_robot_no');
        $audience_robot_no = \Yii::$app->request->post('audience_robot_no');


        if(isset($create_robot_no)){
            if($create_robot_no === 0)
            {
                $rst['message'] = '创建房间时直播间机器人人数不能为空';
                echo json_encode($rst);
                exit;
            }elseif($create_robot_no > 100 ){
                $rst['message'] = '创建房间时直播间机器人人数不能大于100';
                echo json_encode($rst);
                exit;
            }elseif($create_robot_no < 0 ){
                $rst['message'] = '创建房间时直播间机器人人数不能小于0';
                echo json_encode($rst);
                exit;
            }
            $client->create_robot_no = $create_robot_no;
        }else{
            if(empty($audience_robot_no) && $create_robot_no === 0)
            {
                $rst['message'] = '每个观众进入直播间时带来的机器人数量不能为空';
                echo json_encode($rst);
                exit;
            }elseif($audience_robot_no > 50 ){
                $rst['message'] = '每个观众进入直播间时带来的机器人数量不能大于50';
                echo json_encode($rst);
                exit;
            }elseif($audience_robot_no < 0 ){
                $rst['message'] = '每个观众进入直播间时带来的机器人数量不能小于0';
                echo json_encode($rst);
                exit;
            }
            $client->audience_robot_no = $audience_robot_no;
        }
        if(!ClientRobotUtil::SaveClientRobot($client,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        \Yii::$app->cache->delete('get_robot_params_'.$user_id);
        echo '0';


    }
} 