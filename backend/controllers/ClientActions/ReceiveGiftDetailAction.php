<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 19:13
 */
namespace backend\controllers\ClientActions;



use backend\models\ReceiveGiftDetailSearch;
use yii\base\Action;

class ReceiveGiftDetailAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '礼物详情';

        $searchModel = new ReceiveGiftDetailSearch();
        $params = \Yii::$app->request->queryParams;
        if(empty($params['ReceiveGiftDetailSearch']['create_time'])){
            $params['ReceiveGiftDetailSearch']['create_time'] = date('Y-m-d').'|'.date('Y-m-d');
        }
        $dataProvider = $searchModel->search($params);

        $this->controller->layout='main_empty';
        return $this->controller->render('receivegiftdetail',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
}