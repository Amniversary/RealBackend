<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

namespace backend\controllers\PaymentmanageActions;


use backend\models\PaymentSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '支付方式管理';
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
}