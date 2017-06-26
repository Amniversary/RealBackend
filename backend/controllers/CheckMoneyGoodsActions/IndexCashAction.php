<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

namespace backend\controllers\CheckMoneyGoodsActions;


use backend\models\CheckMoneyGoodsSearch;
use yii\base\Action;

class IndexCashAction extends Action
{
    public function run($data_type='unpaid')
    {

        $this->controller->getView()->title = '票提现审核管理';
        $searchModel = new CheckMoneyGoodsSearch();
        if($data_type == 'alreadypaid'){
            $dataProvider = $searchModel->already_paid_search(\Yii::$app->request->queryParams);
            return $this->controller->render('indexalreadypaid',
                [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'data_type' => 'alreadypaid'
                ]
            );
        }else{
            $dataProvider = $searchModel->unpaid_search(\Yii::$app->request->queryParams);
            return $this->controller->render('indexunpaid',
                [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'data_type' => 'unpaid'
                ]
            );
        }

    }
}