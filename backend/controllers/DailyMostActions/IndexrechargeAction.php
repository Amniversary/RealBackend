<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/29
 * Time: 16:32
 */

namespace backend\controllers\DailyMostActions;

use backend\models\DailyMostSearch;
use yii\base\Action;

class IndexrechargeAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '每日之最';
        $searchModel = new DailyMostSearch();

        $params = \Yii::$app->request->queryParams;
        if(empty($params['DailyMostSearch']['recharge_date']))
        {
            $params['DailyMostSearch']['recharge_date'] = date('Y-m-d',time()-24*60*60);
        }

        $dataProvider = $searchModel->RechargeSearch($params);
        return $this->controller->render('indexrecharge',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'data_type' => 'recharge'
            ]
        );
    }
}