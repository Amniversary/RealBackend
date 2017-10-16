<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/11
 * Time: 下午3:52
 */

namespace backend\controllers\LaterActions;


use backend\models\LaterSearch;
use yii\base\Action;

class IndexParamsAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title  = '批量签到消息设置';
        $searchModel = new LaterSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index_params',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
}