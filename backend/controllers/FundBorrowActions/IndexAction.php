<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/23
 * Time: 10:43
 */

namespace backend\controllers\FundBorrowActions;


use backend\models\FundBorrowSearch;
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
        $this->controller->getView()->title = '美愿基金未打款记录';
        $searchModel = new FundBorrowSearch();
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