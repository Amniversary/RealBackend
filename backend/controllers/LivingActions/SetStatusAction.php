<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/21
 * Time: 13:04
 */

namespace backend\controllers\LivingActions;

use frontend\business\ClientUtil;
use yii\base\Action;

class SetStatusAction extends Action
{

    public function run($client_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($client_id))
        {
            $rst['message'] = '用户id不能为空';
            echo json_encode($rst);
            exit;
        }

        $client = ClientUtil::GetClientById($client_id);
        if(!isset($client))
        {
            $rst['message'] = '用户信息不存在';
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

        $status = \Yii::$app->request->post('s1');
        if(!isset($status))
        {
            $rst['message'] = '状态参数为空';
            echo json_encode($rst);
            exit;
        }

        $client->status = $status;
        $seal_reason = '';
        if(!ClientUtil::SetBanUser($client,$seal_reason,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
} 