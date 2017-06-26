<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/25
 * Time: 16:14
 */

namespace backend\controllers\ClientActions;


use backend\models\ClientCoverSearch;
use yii\base\Action;

class ClientCoverIndexAction extends Action
{
    public function run()
    {
        $params = [];
        //page=1&per-page=5
        $page = \Yii::$app->request->getQueryParam('page');
        if(isset($page))
        {
            $params['page'] = $page;
        }
        $per_page = \Yii::$app->request->getQueryParam('per-page');
        if(isset($per_page))
        {
            $params['per-page'] = $per_page;
        }
        \Yii::$app->params['client_pic_index'] = $params;
        $this->controller->getView()->title = '客户封面';
        $searchModel = new ClientCoverSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('coverindex',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
} 