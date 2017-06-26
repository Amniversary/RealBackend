<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 19:13
 */
namespace backend\controllers\DataStatisticActions;


use backend\models\ClientSearch;
use backend\models\MasterProfitForm;
use backend\models\MasterProfitSearch;
use yii\base\Action;

class MasterProfitAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '主播当天收益';
        $searchModel = new MasterProfitSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('master_profit',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
} 