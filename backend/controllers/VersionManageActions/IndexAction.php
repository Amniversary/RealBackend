<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

namespace backend\controllers\VersionManageActions;


use backend\models\MultiVersionInfoSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '版本管理';
        $searchModel = new MultiVersionInfoSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'add_title' => '新增版本',
            ]
        );
    }
}