<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/25
 * Time: 16:14
 */

namespace backend\controllers\BalanceManageActions;


use backend\models\ClientFinanceSearch;
use yii\base\Action;

class InfoAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '客户财务信息';
        $searchModel = new ClientFinanceSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('info',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
} 