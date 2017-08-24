<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/22
 * Time: 下午2:28
 */

namespace backend\controllers\SignActions;

use backend\models\SignImageSearch;
use yii\base\Action;

class BatchIndexMsgAction extends Action
{
    public function run()
    {
        $id = \Yii::$app->request->get('id');
        $this->controller->getView()->title = '签到消息';
        $searchModel = new SignImageSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('batchindexmsg',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider,
            'id'=>$id
        ]);
    }
}