<?php

namespace backend\controllers\EnterRoomNoteActions;


use common\models\EnterRoomNote;
use yii\base\Action;
/**
 * 新增礼物
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class CreateAction extends Action
{
    public function run()
    {
        $model = new EnterRoomNote();
        $this->controller->getView()->title = '新增欢迎词';
        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            return $this->controller->redirect(['index']);
        }
        else
        {
            return $this->controller->render('create', [
                'model' => $model,
            ]);
        }
    }
}