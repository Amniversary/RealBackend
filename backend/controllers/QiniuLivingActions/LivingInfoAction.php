<?php
/**
 * Created by PhpStorm.
 * User: zff
 * Date: 2016/8/8
 * Time: 16:10
 */
namespace backend\controllers\QiniuLivingActions;


use backend\models\LivingInfoSearch;
use yii\base\Action;

class LivingInfoAction extends Action
{
    public function run()
    {
        //echo 'livinginfo';
        $this->controller->getView()->title = '用户直播流查看';
        $searchModel = new LivingInfoSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('livinginfo',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
} 