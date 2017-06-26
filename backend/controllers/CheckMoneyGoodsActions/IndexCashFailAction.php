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

/**
 * 打款失败列表
 * Class IndexCashFailAction
 * @package backend\controllers\CheckMoneyGoodsActions
 */
class IndexCashFailAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '打款失败记录管理';
        $searchModel = new CheckMoneyGoodsSearch();
        $dataProvider = $searchModel->cash_fail(\Yii::$app->request->queryParams);
        return $this->controller->render('index_cash_fail',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );


    }
}