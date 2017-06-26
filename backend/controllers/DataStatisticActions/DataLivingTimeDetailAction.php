<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 19:13
 */
namespace backend\controllers\DataStatisticActions;


use backend\models\LivingTimeDetailSearch;
use yii\base\Action;

class DataLivingTimeDetailAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '主播直播时间详细';
        $searchModel = new LivingTimeDetailSearch();
        $params = \Yii::$app->request->queryParams;
        if(empty($params['LivingTimeDetailSearch']['create_time']) && empty($params['LivingTimeDetailSearch']['finish_time']))
        {
            $params['LivingTimeDetailSearch']['create_time'] = date('Y-m-d 00:00:00').'|'.date('Y-m-d H:i:s');
            $params['LivingTimeDetailSearch']['finish_time'] = date('Y-m-d 00:00:00').'|'.date('Y-m-d H:i:s');
        }
        $dataProvider = $searchModel->search($params);
        return $this->controller->render('living_time_detail',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
}