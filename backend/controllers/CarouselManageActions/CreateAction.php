<?php

namespace backend\controllers\CarouselManageActions;


use common\models\Carousel;
use frontend\business\UpdateContentUtil;
use yii\base\Action;
/**
 * 新增轮播图
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class CreateAction extends Action
{
    public function run()
    {
        $activity_type = require(\Yii::$app->getBasePath().'/config/TemplateConfig.php') ;
        $model = new Carousel();
        $model->status = 1;
        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            UpdateContentUtil::UpdateGiftVersion($error,2);
            return $this->controller->redirect(['index']);
        }
        else
        {
            //var_dump($model);
            return $this->controller->render('create', [
                'model' => $model,
                'activity_type' => $activity_type
            ]);
        }
    }
} 