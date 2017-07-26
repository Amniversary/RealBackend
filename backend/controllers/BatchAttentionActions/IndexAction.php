<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/26
 * Time: 上午11:32
 */

namespace backend\controllers\BatchAttentionActions;


use backend\models\BatchAttentionSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '批量关注回复';
        $searchModel = new BatchAttentionSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider,
        ]);
    }

}