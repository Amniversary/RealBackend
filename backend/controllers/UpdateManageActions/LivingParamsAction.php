<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 11:12
 */

namespace backend\controllers\UpdateManageActions;


use backend\models\LivingParamsSearch;
use yii\base\Action;

class LivingParamsAction extends  Action
{
    public function run()
    {
        $this->controller->getView()->title = '直播参数设置';
        $searchModel = new LivingParamsSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
} 