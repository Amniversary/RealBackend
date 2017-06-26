<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31
 * Time: 13:22
 */

namespace backend\controllers\AdvertorialActions;

use backend\models\AdvertorialSearch;
use yii\base\Action;
use yii;


class IndexAction extends Action
{
    public function run()
    {
        $searchModel = new AdvertorialSearch();
        $dataProvider = $searchModel->search(yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
}

