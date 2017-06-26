<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 19:13
 */
namespace backend\controllers\ClientActions;

use backend\models\ClientSearch;
use yii\base\Action;

class LivingAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '主播信息管理';
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->searchWithLiving(\Yii::$app->request->queryParams);

        return $this->controller->render('living',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
} 