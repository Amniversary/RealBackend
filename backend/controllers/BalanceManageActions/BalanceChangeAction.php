<?php

namespace backend\controllers\BalanceManageActions;
use backend\models\BalanceLogSearch;
use yii\base\Action;

class BalanceChangeAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '鲜花变更详情';
        $searchModel = new BalanceLogSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->controller->render('balance_change',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
}