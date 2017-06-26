<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/14
 * Time: 17:09
 */
namespace backend\controllers\ClientActions;


use backend\models\ApporveClientSearch;
use backend\models\ClientSearch;
use yii\base\Action;

class ApporveUserAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '认证用户信息';
        $searchModel = new ApporveClientSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('apporve_user',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
}