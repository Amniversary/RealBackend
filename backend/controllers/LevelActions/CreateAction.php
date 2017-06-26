<?php

namespace backend\controllers\LevelActions;


use common\models\Goods;
use common\models\LevelStage;
use yii\base\Action;
use yii\log\Logger;

/**
 * 新增商品
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class CreateAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '新增等级';
        $model = new LevelStage();

        if($model->load(\Yii::$app->request->post()) && $model->save())
        {
            \Yii::getLogger()->log('mode:'.var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
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