<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

namespace backend\controllers\IntegralAccountActions;

use backend\models\IntegralAccountSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run(){
        $this->controller->getView()->title = '积分帐户管理';
        $searchModel =  new IntegralAccountSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
}