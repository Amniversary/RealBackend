<?php

namespace backend\controllers\CarouselManageActions;


use backend\components\ExitUtil;
use frontend\business\CarouselUtil;
use frontend\business\UpdateContentUtil;
use yii\base\Action;
/**
 * 修改轮播图
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class UpdateAction extends Action
{
    public function run($carousel_id)
    {
        $activity_type = require(\Yii::$app->getBasePath().'/config/TemplateConfig.php') ;

        $model = CarouselUtil::GetCarouselById($carousel_id);
        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('轮播图记录不存在');
        }
        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            UpdateContentUtil::UpdateGiftVersion($error,2);
            return $this->controller->redirect(['index']);
        }
        else
        {
            return $this->controller->render('update', [
                'model' => $model,
                'activity_type' => $activity_type
            ]);
        }
    }
} 