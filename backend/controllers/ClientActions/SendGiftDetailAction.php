<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 19:13
 */
namespace backend\controllers\ClientActions;



use backend\models\SendGiftDetailSearch;
use yii\base\Action;
use yii\log\Logger;

class SendGiftDetailAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '礼物详情';

        $searchModel = new SendGiftDetailSearch();
        $params = \Yii::$app->request->queryParams;
        if(empty($params['SendGiftDetailSearch']['create_time'])){
            $params['SendGiftDetailSearch']['create_time'] = date('Y-m-d').'|'.date('Y-m-d');
        }
        $dataProvider = $searchModel->search($params);

        $this->controller->layout='main_empty';
        return $this->controller->render('sendgiftdetail',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
}