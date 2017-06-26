<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:17
 */

namespace backend\controllers\CommentmanageActions;


use backend\models\CommentRewardSearch;
use backend\models\MyBillSearch;
use yii\base\Action;
/**
 * Class IndexHistoryAction
 * @package backend\controllers\RedPacketsActions
 */
class IndexHistoryAction extends Action
{
    public function run()
    {
        $data_type = \Yii::$app->request->getQueryParam('data_type');
        if(empty($data_type))
        {
            $data_type = 'undo';
        }
        $this->controller->getView()->title = '打赏备注管理';
        $searchModel = new CommentRewardSearch();
        $params = \Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);
        return $this->controller->render('indexhis',
                [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'data_type'=>$data_type,
                ]
            );
    }
} 