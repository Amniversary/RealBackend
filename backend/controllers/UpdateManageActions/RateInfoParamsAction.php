<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 10:45
 */

namespace backend\controllers\UpdateManageActions;


use backend\models\RateInfoParamsSearch;
use yii\base\Action;

class RateInfoParamsAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '费率信息参数';
        $searchModel = new RateInfoParamsSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
} 