<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 11:12
 */

namespace backend\controllers\UpdateManageActions;


use backend\models\LivingTypeParamsSearch;
use yii\base\Action;

class LivingTypeParamsAction extends  Action
{
    public function run()
    {
        $this->controller->getView()->title = '直播类型参数设置';
        $searchModel = new LivingTypeParamsSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('livingtype',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
} 