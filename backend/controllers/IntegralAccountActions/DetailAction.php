<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

namespace backend\controllers\IntegralAccountActions;


use backend\models\GoldsPrestoreSearch;
use backend\models\GoldsAccountSearch;
use backend\models\IntegralAccountLogSearch;
use yii\base\Action;

class DetailAction extends Action
{
    public function run($integral_account_id){
        $this->controller->getView()->title = '积分帐户明细查询';
        $searchModel = new IntegralAccountLogSearch();
        $params = \Yii::$app->request->getQueryParams();
        if(empty($params['IntegralAccountLogSearch']['create_time'])){
            $params['IntegralAccountLogSearch']['create_time'] = date('Y-m-d 00:00:00').'|'.date('Y-m-d H:i:s');
        }
        $this->controller->layout='main_empty';
        $dataProvider = $searchModel->search($params);
        return $this->controller->render('detail',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
}