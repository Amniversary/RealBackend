<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/22
 * Time: 下午12:03
 */

namespace backend\controllers\SignActions;


use backend\models\SignDaySearch;
use yii\base\Action;

class BatchIndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title  = '批量签到消息设置';
        $searchModel = new SignDaySearch();
        $dataProvider = $searchModel->searchBatch(\Yii::$app->request->queryParams);
        return $this->controller->render('indexbatch',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
}