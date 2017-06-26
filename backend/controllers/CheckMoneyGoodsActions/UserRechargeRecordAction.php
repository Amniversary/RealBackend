<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/27
 * Time: 11:08
 */

namespace backend\controllers\CheckMoneyGoodsActions;


use backend\models\UserRechargeSearch;
use yii\base\Action;

class UserRechargeRecordAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '个人充值管理';
        $searchModel = new UserRechargeSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('indexrecharge',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
} 