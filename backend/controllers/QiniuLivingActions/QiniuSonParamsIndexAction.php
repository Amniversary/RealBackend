<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/28
 * Time: 14:34
 */
namespace backend\controllers\QiniuLivingActions;

use backend\models\QiniuSonParamsSearch;
use yii\base\Action;

class QiniuSonParamsIndexAction extends Action
{
    public function run()
    {
        $this->controller->view->title = '七牛直播详细设置';
        $searchModel = new QiniuSonParamsSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
} 