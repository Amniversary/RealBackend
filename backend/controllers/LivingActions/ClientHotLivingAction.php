<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/16
 * Time: 10:05
 */

namespace backend\controllers\LivingActions;


use backend\models\ClientHotLivingSearch;
use yii\base\Action;

class ClientHotLivingAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '热门直播';
        $searchModel = new ClientHotLivingSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('hotliving',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
} 