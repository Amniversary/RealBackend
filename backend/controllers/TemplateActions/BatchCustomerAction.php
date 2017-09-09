<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/7
 * Time: 上午11:58
 */

namespace backend\controllers\TemplateActions;


use backend\models\BatchCustomerSearch;
use yii\base\Action;

class BatchCustomerAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '群发消息配置';
        $searchModel = new BatchCustomerSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('batch_customer',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
}