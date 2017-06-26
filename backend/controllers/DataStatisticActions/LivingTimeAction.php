<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 19:13
 */
namespace backend\controllers\DataStatisticActions;


use backend\models\ClientSearch;
use backend\models\LivingTimeSearch;
use yii\base\Action;

class LivingTimeAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '主播直播时间';
        $searchModel = new LivingTimeSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('living_time',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
} 