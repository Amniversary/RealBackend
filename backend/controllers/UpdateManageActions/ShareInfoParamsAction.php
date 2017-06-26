<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 10:23
 */

namespace backend\controllers\UpdateManageActions;


use backend\models\ShareInfoParamsSearch;
use yii\base\Action;

class ShareInfoParamsAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '分享信息参数';
        $searchModel = new ShareInfoParamsSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
} 