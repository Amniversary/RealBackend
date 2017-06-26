<?php

namespace backend\controllers\UserManageActions;


use backend\models\BackendUserSearch;
use yii\base\Action;
/**
 * 人员列表
 * Class IndexAction
 * @package backend\controllers\UserManageActions
 */
class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '人员管理';
        $searchModel = new BackendUserSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
            echo $this->controller->render('index',
                [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]
            );
    }
} 