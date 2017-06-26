<?php

namespace backend\controllers\AdvertisingManageActions;

use backend\models\AdvertisingSearch;
use yii\base\Action;

/**
 * 弹窗广告图列表
 * Class IndexAction
 * @package backend\controllers\AdvertisingManageActions
 */
class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '弹窗广告图管理';
        $searchModel = new AdvertisingSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
            echo $this->controller->render('index',
                [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]
            );
    }
} 