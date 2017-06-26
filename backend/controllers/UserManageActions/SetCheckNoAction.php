<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 15:10
 */

namespace backend\controllers\UserManageActions;



use backend\business\SetUserCheckNoUtil;
use backend\business\UserUtil;
use backend\components\ExitUtil;
use backend\models\ResetPwdForm;
use common\models\SetUserCheckNo;
use yii\base\Action;

class SetCheckNoAction extends Action
{
    public function run($user_id)
    {
        if(empty($user_id))
        {
            ExitUtil::ExitWithMessage('用户id不能为空');
        }
        $user = UserUtil::GetUserByUserId($user_id);
        if(!isset($user))
        {
            ExitUtil::ExitWithMessage('用户不存在');
        }
        $model = SetUserCheckNoUtil::GetUserCheckNoByUserId($user_id);
        if(!isset($model))
        {
            $model = new SetUserCheckNo();
            $model->user_id = $user_id;
        }
        $params = \Yii::$app->request->post('SetUserCheckNo');
        if(isset($params))
        {
            $model->attributes = $params;
            if($model->validate())
            {
                $rst = ['code'=>'1','msg'=>''];
                $error = '';

                if(!SetUserCheckNoUtil::Save($model,$error))
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
                foreach($model->getErrors() as $key=>$value)
                {
                    $error = $value[0];
                    break;
                }
                $rst['msg'] = $error;
                echo json_encode($rst);
                exit;
            }
        }
        $this->controller->layout='main_empty';

        return $this->controller->render('setcheckno',['user'=>$user,'model'=>$model]);
    }
} 