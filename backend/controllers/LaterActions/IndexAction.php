<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/11
 * Time: 上午11:55
 */

namespace backend\controllers\LaterActions;


use backend\models\BatchKeywordSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '早晚安关键字';
        $searchModel = new BatchKeywordSearch();
        $dataProvider = $searchModel->searchLater(\Yii::$app->request->queryParams);
        return $this->controller->render('keyword', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
}