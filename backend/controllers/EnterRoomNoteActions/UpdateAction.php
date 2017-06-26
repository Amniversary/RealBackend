<?php

namespace backend\controllers\EnterRoomNoteActions;


use backend\components\ExitUtil;
use frontend\business\EnterRoomNoteUtil;
use yii\base\Action;
/**
 * 修改欢迎词
 * Class CreateAction
 * @package backend\controllers\UpdateAction
 */
class UpdateAction extends Action
{
    public function run($record_id)
    {
        $model = EnterRoomNoteUtil::GetEnterRoomNoteById($record_id);

        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('欢迎词不存在');
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