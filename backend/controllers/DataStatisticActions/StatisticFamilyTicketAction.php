<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 19:13
 */
namespace backend\controllers\DataStatisticActions;


use backend\models\StatisticFamilyTicketSearch;
use yii\base\Action;

class StatisticFamilyTicketAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '家族票数统计列表';
        $searchModel = new StatisticFamilyTicketSearch();
        $params = \Yii::$app->request->queryParams;
        if(empty($params['StatisticFamilyTicketSearch']['create_time']))
        {
            $params['StatisticFamilyTicketSearch']['create_time'] = date('Y-m-d').'|'.date('Y-m-d');
        }
        $dataProvider = $searchModel->search($params);
        return $this->controller->render('family_ticket',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
}