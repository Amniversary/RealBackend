<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 11:09
 */

namespace backend\controllers\UpdateManageActions;


use backend\models\StatisticsInfoParamsSearch;
use yii\base\Action;

class StatisticsInfoParamsAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '统计信息参数';
        $searchModel = new StatisticsInfoParamsSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
} 