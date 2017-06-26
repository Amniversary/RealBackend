<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/28
 * Time: 15:35
 */

namespace backend\controllers\QiniuLivingActions;


use backend\models\QiniuClientParamsSearch;
use yii\base\Action;

class QiniuClientParamsAction extends Action
{
    public function run()
    {
        $this->controller->view->title = '七牛用户参数设置';
        $searchModel = new QiniuClientParamsSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('clientparams', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
} 