<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/21
 * Time: 上午11:16
 */

namespace backend\controllers\SignActions;


use backend\models\SignDaySearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '签到消息设置';
        $searchModel = new SignDaySearch();
        $dateProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',[
            'searchModel' => $searchModel,
            'dataProvider' => $dateProvider
        ]);
    }
}