<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/16
 * Time: 下午10:24
 */

namespace backend\controllers\BatchKeyWordActions;


use backend\models\BatchKeywordSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '批量关键词设置';
        $searchModel = new BatchKeywordSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider,
        ]);
    }
}