<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

namespace backend\controllers\GoldsPrestoreActions;


use backend\models\GoldsPrestoreSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run(){
        $this->controller->getView()->title = '个人金币充值管理';
        $searchModel = new GoldsPrestoreSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('indexprestore',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
}