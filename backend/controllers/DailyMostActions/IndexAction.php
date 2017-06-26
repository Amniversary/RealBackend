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

class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '每日之最';
        $searchModel = new DailyMostSearch();

        $params = \Yii::$app->request->queryParams;
        if(empty($params['DailyMostSearch']['real_tickets_date']))
        {
            $params['DailyMostSearch']['real_tickets_date'] = date('Y-m-d',time()-24*60*60);
        }

        $dataProvider = $searchModel->search($params);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'data_type' => ['recharge' => 'recharge','gift' => 'gift']
            ]
        );
    }
}