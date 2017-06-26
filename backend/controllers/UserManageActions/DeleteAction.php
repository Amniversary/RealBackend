<?php

namespace backend\controllers\UserManageActions;

use common\models\User;
use common\models\UserMenu;
use yii\base\Action;
use yii\base\Exception;

class DeleteAction extends Action
{
    public function run($user_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($user_id)) {
            $rst['msg']='用户id不能为空';
            echo json_encode($rst);
            exit;
        }
        $user = User::findOne(['backend_user_id'=>$user_id]);
        if(!isset($user))
        {
            $rst['msg']='用户不存在';
            echo json_encode($rst);
            exit;
        }
        if($user->backend_user_id === '1' || $user->username === 'admin') {
            $rst['msg']='无法删除管理员';
            echo json_encode($rst);
            exit;
        }
        try{
            $tans = \Yii::$app->db->beginTransaction();
            (new UserMenu())->deleteAll(['user_id'=>$user_id]);
            if($user->delete() === false) {
                $rst['msg']='删除失败';
                \Yii::error('删除失败:'.var_export($user->getErrors(),true));
                echo json_encode($rst);
                exit;
            }
            $tans->commit();
        }catch (Exception $e){
            $tans->rollBack();
            $rst['msg'] = $e->getMessage();
            echo json_encode($rst);
            exit;
        }

        return $this->controller->redirect('/usermanage/index');
    }
}
//$.fn.yiiGridView.update('apply-grid');