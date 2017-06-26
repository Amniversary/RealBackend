<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/1
 * Time: 14:45
 */

namespace backend\controllers\CloseLivingActions;


use backend\models\CloseUserRecordSearch;
use yii\base\Action;

class CloseIndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '用户禁用记录信息';
        $searchModel = new CloseUserRecordSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('closeindex',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
} 