<?php

namespace backend\controllers\BalanceManageActions;
use backend\models\BalanceLogSearch;
use yii\base\Action;

class TicketChangeAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '余票变更详情';
        $searchModel = new BalanceLogSearch();
        $dataProvider = $searchModel->ticketSearch(\Yii::$app->request->queryParams);

        return $this->controller->render('ticket_change',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
}