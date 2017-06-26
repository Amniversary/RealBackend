<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

namespace backend\controllers\GoldsAccountActions;


use backend\models\GoldsPrestoreSearch;
use backend\models\GoldsAccountSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run(){
        $this->controller->getView()->title = '金币帐户管理';
        $searchModel = new GoldsAccountSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
}