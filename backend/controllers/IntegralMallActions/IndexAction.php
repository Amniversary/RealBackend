<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11
 * Time: 13:54
 */
namespace backend\controllers\IntegralMallActions;


use backend\models\IntegralMallSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '积分商城礼品管理';
        $searchModel = new IntegralMallSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
}