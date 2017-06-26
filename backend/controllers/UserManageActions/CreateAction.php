<?php
namespace backend\controllers\UserManageActions;

use common\models\User;
use yii\base\Action;
/**
 * 新增管理员
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class CreateAction extends Action
{
    public function run()
    {
        $model = new User();
        $model->scenario = 'create';
        $model->pic = '';
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->controller->redirect(['index']);
        } else {
            return $this->controller->render('create', [
                'model' => $model,
            ]);
        }
    }
} 