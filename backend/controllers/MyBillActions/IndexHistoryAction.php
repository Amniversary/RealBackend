<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:17
 */

namespace backend\controllers\MyBillActions;


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
        $this->controller->getView()->title = '美愿基金历史账单记录';
        $searchModel = new MyBillSearch();
        $params = \Yii::$app->request->queryParams;
        $dataProvider = $searchModel->searchHistory($params);
        return $this->controller->render('indexhis',
                [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'data_type'=>$data_type,
                ]
            );
    }
} 