<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

namespace backend\controllers\ApproveBusinessCheckActions;


use backend\business\UserUtil;
use frontend\business\ApproveUtil;
use yii\base\Action;

class CheckBatchBusinessCheckActions extends Action
{
    public function run()
    {
        $ids = \Yii::$app->request->post('data');
        $refuesd_reason = \Yii::$app->request->post('refused_reason');
        $check_rst = \Yii::$app->request->post('check_res');
        $ids = explode('-',$ids);
        $rst['code'] = 0;
        if(empty($ids)){
            $rst['code'] = 1;
            $rst['msg'] = '审核id不能空';
            echo json_encode($rst);
            exit;
        }

        if(($check_rst == 0) && empty($refuesd_reason)){
            $rst['code'] = 1;
            $rst['msg']='拒绝原因不能为空';
            echo json_encode($rst);
            exit;
        }
        $user = UserUtil::GetUserByUserId(\Yii::$app->user->id);
        if(!isset($user))
        {
            $rst['code'] = 1;
            $rst['msg']='后台用户不存在';
            echo json_encode($rst);
            exit;
        }

        $ids_len = count($ids);

        foreach($ids as $id)
        {
            $approve_info = ApproveUtil::GetApproveById($id);
            $params = [
                'approve_id'=>$id,
                'admin_user_id'=> \Yii::$app->user->id,
                'refuesd_reason'=>$refuesd_reason,
                'check_rst' => $check_rst,
                'admin_username' => $user->username,
                'user_id' => $approve_info->client_id
            ];
            if(!ApproveUtil::CheckRefuse($params,$error))
            {
                continue;
            }

            $ids_len--;
        }

        if(($ids_len>0) && ($ids_len === count($ids))){
            $rst['code'] = 1;
            $rst['msg']='审核失败';
            echo json_encode($rst);
            exit;
        }

        $rst['code']='0';
        echo json_encode($rst);
    }
}