<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/30
 * Time: 13:47
 */

namespace backend\controllers\CloseLivingActions;


use backend\models\CloseOpenUserRecordSearch;
use yii\base\Action;

class CloseOpenIndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '用户解封记录信息';
        $searchModel = new CloseOpenUserRecordSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('closeopenindex',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
}