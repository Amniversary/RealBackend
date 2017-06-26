<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 20:08
 */

namespace backend\controllers\FamilyActions;


use backend\business\UserUtil;
use backend\components\ExitUtil;
use yii\base\Action;

class UpdateAction extends Action
{
    public function run($family_id)
    {
        $model = UserUtil::GetFamilyById($family_id);
        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('家族账号不存在');
        }

        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            return $this->controller->redirect(['index']);
        }
        else
        {
            return $this->controller->render('update', [
                'model' => $model,
            ]);
        }
    }
} 