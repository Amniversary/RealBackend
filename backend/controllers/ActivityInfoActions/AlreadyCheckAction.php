<?php
/*
 * Created By SublimeText3
 * User: hlq
 * Date: 2016/8/27
 * Time: 11:00
 */

namespace backend\controllers\ActivityInfoActions;

use backend\models\CheckEnrollSearch;
use yii\base\Action;
use yii;

/**
 * 已审核/已拒绝
 * Class AlreadyCheckAction
 * @package backend\controllers\ActivityInfoActions
 */
class AlreadyCheckAction extends Action
{
    public function run()
    {
        $searchModel = new CheckEnrollSearch();
        $dataProvider = $searchModel->already_search(yii::$app->request->queryParams);
        return $this->controller->render('index_check_already',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'data_type' => 'already'
            ]
        );
    }
}

