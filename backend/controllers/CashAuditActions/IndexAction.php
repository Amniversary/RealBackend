<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/11/3
 * Time: 下午4:01
 */

namespace backend\controllers\CashAuditActions;


use backend\models\CashAuditSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '小程序提现审核';
        $searchModel = new CashAuditSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
}