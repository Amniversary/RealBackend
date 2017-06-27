<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/12
 * Time: 16:46
 */

namespace backend\controllers\UpdateManageActions;


use backend\models\UserDeviceParamsSearch;
use yii\base\Action;

class UserDeviceParamsAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '用户设备参数';
        $searchModel = new UserDeviceParamsSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
} 