<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 15:10
 */

namespace backend\controllers\UserManageActions;



use backend\business\UserUtil;
use backend\components\ExitUtil;
use backend\models\ResetPwdForm;
use yii\base\Action;

class ResetPwdAction extends Action
{
    public function run($user_id)
    {
        if(empty($user_id)) {
            ExitUtil::ExitWithMessage('用户id不能为空');
        }
        $user = UserUtil::GetUserByUserId($user_id);
        if(!isset($user)) {
            ExitUtil::ExitWithMessage('用户不存在');
        }
        $model = new ResetPwdForm();
        $params = \Yii::$app->request->post('ResetPwdForm');
        if(isset($params))
        {
            $model->attributes = $params;
            if($model->validate()) {
                $rst = ['code'=>'1','msg'=>''];
                $error = '';
                $user->password = $model->newpwd;
                if(!UserUtil::ResetPwd($user,$error))
                {
                    $rst['msg'] = $error;
                    echo json_encode($rst);
                    exit;
                }
                $rst['code']='0';
                echo json_encode($rst);
                exit;
            } else {
                $rst['msg'] = $model->getFirstError('newpwd');
                echo json_encode($rst);
                exit;
            }
        }
        $this->controller->layout='main_empty';
        return $this->controller->render('resetpwd',[
            'user'=>$user,
            'model'=>$model
        ]);
    }
} 