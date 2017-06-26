<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/12
 * Time: 16:48
 */

namespace backend\controllers\SendMsgActions;

use yii\base\Action;
use backend\models\SendmsgSearch;

class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '推送记录';
        $searchModel = new SendmsgSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->controller->render('index',
            [
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
}