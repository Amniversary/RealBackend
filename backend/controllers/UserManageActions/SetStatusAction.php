<?php

namespace backend\controllers\UserManageActions;

use backend\business\UserUtil;
use backend\components\ExitUtil;
use backend\models\ResetPwdForm;
use yii\base\Action;
use yii\base\Exception;
use yii\web\HttpException;

class SetStatusAction extends Action
{
    public function run($user_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($user_id)) {
            $rst['message'] = '用户id不能为空';
            echo json_encode($rst);
            exit;
        }
        $user = UserUtil::GetUserByUserId($user_id);
        if(!isset($user)) {
            $rst['message'] = '用户不存在';
            echo json_encode($rst);
            exit;
        }
        $hasEdit = \Yii::$app->request->post('hasEditable');
        if(!isset($hasEdit)) {
            $rst['message'] = 'hasEditable参数为空';
            echo json_encode($rst);
            exit;
        }
        $editIndex = \Yii::$app->request->post('editableIndex');
        if(!isset($editIndex)) {
            $rst['message'] = 'editableIndex参数为空';
            echo json_encode($rst);
            exit;
        }
        $modifyData = \Yii::$app->request->post('User');
        if(!isset($modifyData)) {
            $rst['message'] = '没有User模型对应的数据';
            echo json_encode($rst);
            exit;
        }

        if(!isset($modifyData[$editIndex])) {
            $rst['message'] = '对应的列下没有数据';
            echo json_encode($rst);
            exit;
        }
        $dataItem = $modifyData[$editIndex];
        if(!isset($dataItem['status'])) {
            $rst['message'] = '状态值为空';
            echo json_encode($rst);
            exit;
        }
        $status = $dataItem['status'];
        if(($user->backend_user_id === 1 || $user->username === 'admin') && $status == 0) {
            $rst['message'] = '超级管理员不能禁用';
            echo json_encode($rst);
            exit;
        }
        $user->status = $status;
        if(!UserUtil::SaveUser($user, $error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
} 