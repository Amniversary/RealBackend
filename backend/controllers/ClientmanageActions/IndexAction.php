<?php

namespace backend\controllers\ClientmanageActions;

use backend\models\AccountInfoSearch;
use backend\models\ClientSearch;
use common\components\UsualFunForStringHelper;
use yii\base\Action;
/**
 * 举报列表
 * Class IndexAction
 * @package backend\controllers\UserManageActions
 */
class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '客户管理';
        $searchModel = new ClientSearch();// new AccountInfoSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        \Yii::$app->session['unique_id'] = UsualFunForStringHelper::CreateGUID();
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
} 