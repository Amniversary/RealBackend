<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:17
 */

namespace backend\controllers\FundBorrowActions;


use backend\models\FundBorrowSearch;
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
        $this->controller->getView()->title = '美愿基金已打款记录';
        $searchModel = new FundBorrowSearch();
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