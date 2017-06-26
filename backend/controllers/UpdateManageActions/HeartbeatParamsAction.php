<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 11:13
 */

namespace backend\controllers\UpdateManageActions;


use backend\models\HeartbeatParamsSearch;
use yii\base\Action;

class HeartbeatParamsAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '心跳参数设置';
        $searchModel = new HeartbeatParamsSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
} 