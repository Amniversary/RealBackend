<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/25
 * Time: 16:14
 */

namespace backend\controllers\ClientActions;


use backend\models\ClientRobotSearch;
use yii\base\Action;

class ClientRobotIndexAction extends Action
{
    public function run()
    {

        $this->controller->getView()->title = '直播机器人设置';
        $searchModel = new ClientRobotSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('robotindex',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
} 