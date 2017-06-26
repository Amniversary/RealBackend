<?php

namespace backend\controllers\CarouselManageActions;

use backend\models\CarouselSearch;
use yii\base\Action;
/**
 * 轮播图列表
 * Class IndexAction
 * @package backend\controllers\UserManageActions
 */
class IndexAction extends Action
{
    public function run()
    {
        $activity_type = require(\Yii::$app->getBasePath().'/config/TemplateConfig.php') ;
        $this->controller->getView()->title = '轮播图管理';
        $searchModel = new CarouselSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
            echo $this->controller->render('index',
                [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'activity_type' => $activity_type
                ]
            );
    }
} 