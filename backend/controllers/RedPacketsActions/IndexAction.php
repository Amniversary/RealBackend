<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:17
 */

namespace backend\controllers\RedPacketsActions;


use yii\base\Action;
use backend\models\RedPacketsSearch;
/**
 * 红包列表
 * Class IndexAction
 * @package backend\controllers\RedPacketsActions
 */
class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '红包管理';
        $searchModel = new RedPacketsSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
            echo $this->controller->render('index',
                [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]
            );
    }
} 