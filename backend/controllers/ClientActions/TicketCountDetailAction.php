<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 19:13
 */
namespace backend\controllers\ClientActions;


use backend\models\ClientCalanceLogSearch;
use yii\base\Action;

class TicketCountDetailAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '客户信息管理';

        $searchModel = new ClientCalanceLogSearch();
        $params = \Yii::$app->request->queryParams;
        if(empty($params['ClientCalanceLogSearch']['create_time'])){
            $params['ClientCalanceLogSearch']['create_time'] = date('Y-m-d').'|'.date('Y-m-d');
        }
        //var_dump($params);
        $dataProvider = $searchModel->ticketSearch($params);
        $this->controller->layout='main_empty';
        return $this->controller->render('ticket_detail',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
}