<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

namespace backend\controllers\CheckReportActions;


use backend\models\CheckMoneyGoodsSearch;
use backend\models\CheckReportSearch;
use yii\base\Action;

class IndexAuditedAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '举报审核管理';
        $searchModel = new CheckReportSearch();
        $dataProvider = $searchModel->AuditeSearch(\Yii::$app->request->queryParams);
        return $this->controller->render('indexaudited',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'data_type' => 'audited'
            ]
        );
    }
}