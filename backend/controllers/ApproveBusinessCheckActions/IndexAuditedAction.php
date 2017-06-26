<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

namespace backend\controllers\ApproveBusinessCheckActions;


use backend\models\ApproveBusinessCheckSearch;
use yii\base\Action;

class IndexAuditedAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '高级直播认证审核管理';
        $searchModel = new ApproveBusinessCheckSearch();
        $dataProvider = $searchModel->aditedsearch(\Yii::$app->request->queryParams);
        return $this->controller->render('indexaudited',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'data_type' => 'audited'
            ]
        );
    }
}