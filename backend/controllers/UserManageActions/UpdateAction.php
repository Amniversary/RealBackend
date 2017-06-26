<?php

namespace backend\controllers\UserManageActions;


use backend\business\UserUtil;
use backend\components\ExitUtil;
use yii\base\Action;
/**
 * 修改人员信息
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class UpdateAction extends Action
{
    public function run($user_id)
    {
        $model = UserUtil::GetUserByUserId($user_id);
        if(!isset($model)) {
            ExitUtil::ExitWithMessage('用户不存在');
        }
        
        $model->pic = empty($model->pic)? 'http://oss.aliyuncs.com/meiyuan/wish_type/default.png':$model->pic;
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->controller->redirect(['index']);
        } else {
            return $this->controller->render('update', [
                'model' => $model,
            ]);
        }
    }
} 