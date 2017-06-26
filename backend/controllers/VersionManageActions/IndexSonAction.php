<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

namespace backend\controllers\VersionManageActions;


use backend\models\MultiUpdateContentSearch;
use yii\base\Action;

class IndexSonAction extends Action
{
    public function run($app_id)
    {
        $this->controller->getView()->title = '子版本管理';
        $searchModel = new MultiUpdateContentSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('indexson',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'add_title' => '新增子版本',
                'app_id' => $app_id
            ]
        );
    }
}