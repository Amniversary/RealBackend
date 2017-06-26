<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/8
 * Time: 16:32
 */

namespace backend\controllers\ClientalbumActions;

use backend\models\ClientalbumSearch;
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
        $this->controller->getView()->title = '客户相册管理';
        $searchModel = new ClientalbumSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
}