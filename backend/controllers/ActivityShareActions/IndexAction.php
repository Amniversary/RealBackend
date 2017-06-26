<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/28
 * Time: 14:11
 */

namespace backend\controllers\ActivityShareActions;


use backend\models\ActivityShareSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '活动分享设置';
        $searchModel = new ActivityShareSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
} 