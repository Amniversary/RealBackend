<?php

namespace backend\controllers\ApproveElementaryActions;


use backend\business\UserUtil;
use frontend\business\ApproveUtil;
use frontend\business\TicketToCashUtil;
use yii\base\Action;

/**
 * 审核拒绝、通过
 * Class CreateAction
 * @package backend\controllers\UpdateAction
 */
class CheckRefuseAction extends Action
{
    public function run()
    {
        $error = '';
        $approve_id = \Yii::$app->request->post('check_id');
        $refuesd_reason = \Yii::$app->request->post('refused_reason');
        $check_rst = \Yii::$app->request->post('check_res');
        if(empty($approve_id))
        {
            $rst['msg']='审核记录id为空，数据异常';
            echo json_encode($rst);
            exit;
        }

        if(!isset($check_rst) || !in_array($check_rst,[0,1]))
        {
            $rst['msg']='审核结果值异常';
            echo json_encode($rst);
            exit;
        }

        $model = ApproveUtil::GetApproveElementaryById($approve_id);
        if(!isset($model))
        {
            $rst['msg']='审核记录不存在';
            echo json_encode($rst);
            exit;
        }

        if($model['status'] == 1)
        {
            $rst['msg']='该记录已经审核过了';
            echo json_encode($rst);
            exit;
        }

        $user = UserUtil::GetUserByUserId(\Yii::$app->user->id);
        if(!isset($user))
        {
            $rst['msg']='后台用户不存在';
            echo json_encode($rst);
            exit;
        }

        $params = [
            'approve_id'=>$approve_id,
            'admin_user_id'=> \Yii::$app->user->id,
            'refuesd_reason'=>$refuesd_reason,
            'check_rst' => $check_rst,
            'admin_username' => $user->username,
            'user_id' => $model['client_id'],
            'status' => 4,
            'business_check_id' => $model['business_check_id'],
        ];

        if(!ApproveUtil::CheckRefuse($params,$error))
        {
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }
        $rst['code']='0';
        echo json_encode($rst);

    }
}