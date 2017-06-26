<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/19
 * Time: 13:12
 */

namespace backend\controllers\ChangeRecordActions;


use backend\models\ChangeRecordSearch;
use backend\models\CheckMoneyGoodsSearch;
use yii\base\Action;

class IndexExamineAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '兑换积分商品审核';
        $searchModel = new ChangeRecordSearch();
        $dataProvider = $searchModel->examineSearch(\Yii::$app->request->queryParams);
        return $this->controller->render('indexexamine',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'data_type' => 'examine'
            ]
        );
    }
}