<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/23
 * Time: 上午11:24
 */

namespace backend\controllers\SignActions;


use backend\models\SignUserSearch;
use yii\base\Action;

class SignUserAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '用户签到数据';
        $searchModel = new SignUserSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('user',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}