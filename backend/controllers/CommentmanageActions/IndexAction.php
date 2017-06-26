<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/23
 * Time: 10:43
 */

namespace backend\controllers\CommentmanageActions;


use backend\models\CommentSearch;
use yii\base\Action;

/**
 * Class IndexAction
 * @package backend\controllers\GetCashActions
 */
class IndexAction extends Action
{
    public function run()
    {
        $data_type = \Yii::$app->request->getQueryParam('data_type');
        if(empty($data_type))
        {
            $data_type = 'undo';
        }
        $this->controller->getView()->title = '愿望评论管理';
        $searchModel = new CommentSearch();
        $params = \Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);
        return $this->controller->render('indexundo',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'data_type'=>$data_type,
            ]
        );
    }
} 