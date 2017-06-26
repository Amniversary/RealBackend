<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 13:54
 */
namespace backend\controllers\AdvertiseActions;


use common\models\Advertise;
use backend\models\AdvertiseSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = 'App启动首页管理';
        $searchModel = new AdvertiseSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
}