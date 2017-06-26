<?php

namespace backend\controllers\HotwordsActions;

use backend\models\HotWordsSearch;
use yii\base\Action;
/**
 * 热词列表
 * Class IndexAction
 * @package backend\controllers\UserManageActions
 */
class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '热词管理';
        $searchModel = new HotWordsSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
} 