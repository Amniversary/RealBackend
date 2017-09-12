<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/9
 * Time: 下午4:59
 */

namespace backend\controllers\TemplateActions;


use backend\models\StatusIndexSearch;
use yii\base\Action;

class IndexStatusAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '状态查看';
        $searchModel = new StatusIndexSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index_status',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}