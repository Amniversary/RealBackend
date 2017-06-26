<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 19:13
 */
namespace backend\controllers\QiniuLivingActions;


use backend\models\LivingParameterSearch;
use yii\base\Action;

class LivingParameterAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '七牛参数信息管理';
        $searchModel = new LivingParameterSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
} 