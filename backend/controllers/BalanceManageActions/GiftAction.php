<?php

namespace backend\controllers\BalanceManageActions;
use backend\models\GiftLogSearch;
use yii\base\Action;

class GiftAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '礼物打赏详情';
        $searchModel = new GiftLogSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        // var_dump( $dataProvider );
        // exit();
        return $this->controller->render('gift',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
}