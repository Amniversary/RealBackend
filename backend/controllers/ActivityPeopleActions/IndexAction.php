<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/26
 * Time: 21:08
 */

namespace backend\controllers\ActivityPeopleActions;


use backend\models\ActivityPeopleSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '预告主播设置';
        $searchModel = new ActivityPeopleSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
} 