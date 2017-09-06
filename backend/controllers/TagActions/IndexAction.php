<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/5
 * Time: 下午4:23
 */

namespace backend\controllers\TagActions;


use backend\models\TagSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '标签管理';
        $searchModel = new TagSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}