<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/9
 * Time: 下午5:35
 */

namespace backend\controllers\ArticleActions;


use backend\models\ArticleOrderSearch;
use yii\base\Action;

class ArticleOrderAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '图文乱序设置';
        $searchModel = new ArticleOrderSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index_order',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
}