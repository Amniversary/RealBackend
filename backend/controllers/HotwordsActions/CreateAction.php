<?php

namespace backend\controllers\HotwordsActions;


use common\models\HotWords;
use yii\base\Action;
/**
 * 新增热词
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class CreateAction extends Action
{
    public function run()
    {
        $model = new HotWords();
        $model->status = 1;
        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            return $this->controller->redirect(['index']);
        }
        else
        {
            //var_dump($model);
            return $this->controller->render('create', [
                'model' => $model,
            ]);
        }
    }
} 