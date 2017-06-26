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

class NospeakingAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '客户禁言管理';
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('nospeaking',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
} 