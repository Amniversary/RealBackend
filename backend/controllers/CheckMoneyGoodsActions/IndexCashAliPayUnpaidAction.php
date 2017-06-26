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
 * 支付宝未打款列表
 * Class IndexCashAliPayUnpaidAction
 * @package backend\controllers\CheckMoneyGoodsActions
 */
class IndexCashAliPayUnpaidAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '支付宝未打款管理';
        $searchModel = new CheckMoneyGoodsSearch();
        $dataProvider = $searchModel->alipay_unpaid(\Yii::$app->request->queryParams);
        return $this->controller->render('index_alipay_list',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'data_type' => 'unpaid',
            ]
        );


    }
}