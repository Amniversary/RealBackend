<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11
 * Time: 17:03
 */
namespace backend\controllers\ChangeRecordActions;


use backend\models\ChangeRecordSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '兑换积分商品审核';
        $searchModel = new ChangeRecordSearch();
        $dataProvider = $searchModel->noexamineSearch(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'data_type' => 'noexamine'
            ]
        );
    }
}