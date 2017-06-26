<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:17
 */

namespace backend\controllers\AuditmanageActions;


use yii\base\Action;
use backend\models\BusinessCheckSearch;
/**
 * 红包列表
 * Class IndexAction
 * @package backend\controllers\RedPacketsActions
 */
class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '审核管理';
        $searchModel = new BusinessCheckSearch();
        $params = \Yii::$app->request->queryParams;
        $params['BusinessCheckSearch']['status']='0';
        $dataProvider = $searchModel->search($params);
        return $this->controller->render('index',
                [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]
            );
    }
} 