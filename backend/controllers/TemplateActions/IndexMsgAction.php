<?php

namespace backend\controllers\TemplateActions;


use backend\models\KeyWordMsgSearch;
use yii\base\Action;

class IndexMsgAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '任务消息配置';
        $id = \Yii::$app->request->get('id');
        $searchModel = new KeyWordMsgSearch();
        $dataProvider = $searchModel->searchBatchParams(\Yii::$app->request->queryParams);
        return $this->controller->render('customer_msg', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'id' => $id
        ]);
    }
}