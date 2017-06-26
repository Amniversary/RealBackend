<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 10:28
 */

namespace backend\controllers\UpdateManageActions;


use backend\models\SignRewardParamsSearch;
use yii\base\Action;

class SignRewardParamsAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '签到打赏参数';
        $searchModel = new SignRewardParamsSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
} 