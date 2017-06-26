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
 * 未审核
 * Class IndexCheckAction
 * @package backend\controllers\ActivityInfoActions
 */
class IndexCheckAction extends Action
{
    public function run()
    {
        $searchModel = new CheckEnrollSearch();
        $dataProvider = $searchModel->search(yii::$app->request->queryParams);
        return $this->controller->render('index_check',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'data_type' => 'check'
            ]
        );
    }
}

