<?php

namespace backend\controllers\ReportManageActions;

use backend\models\ReportSearch;
use yii\base\Action;
/**
 * 举报列表
 * Class IndexAction
 * @package backend\controllers\UserManageActions
 */
class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '举报管理';
        $searchModel = new ReportSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
} 