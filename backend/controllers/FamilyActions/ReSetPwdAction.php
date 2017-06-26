<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 20:47
 */

namespace backend\controllers\FamilyActions;


use backend\business\UserUtil;
use backend\models\ResetPwdForm;
use yii\base\Action;

class ReSetPwdAction extends Action
{
    public function run($family_id)
    {
        if(empty($family_id))
        {
            $rst['msg'] = '家族长id不能为空';
            echo json_encode($rst);
            exit;
        }
        $Family = UserUtil::GetFamilyById($family_id);
        if(!isset($Family))
        {
            $rst['msg'] = '家族长信息不存在';
            echo json_encode($rst);
            exit;
        }
        $model = new ResetPwdForm();
        $params = \Yii::$app->request->post('ResetPwdForm');
        if(isset($params))
        {
            $model->attributes = $params;
            if($model->validate())
            {
                $rst = ['code'=>'1','msg'=>''];
                $error = '';
                $Family->password = $model->newpwd;
                if(!UserUtil::FamilyResetPwd($Family,$error))
                {
                    $rst['msg'] = $error;
                    echo json_encode($rst);
                    exit;
                }
                $rst['code']='0';
                echo json_encode($rst);
                exit;
            }
            else
            {
                $rst['msg'] = $model->getFirstError('newpwd');
                echo json_encode($rst);
                exit;
            }
        }
        $this->controller->layout='main_empty';

        return $this->controller->render('resetpwd', [
            'user'=>$Family,'model'=>$model
        ]);
    }
} 