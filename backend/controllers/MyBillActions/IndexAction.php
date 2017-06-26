<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/23
 * Time: 10:43
 */

namespace backend\controllers\MyBillActions;


use backend\models\MyBillSearch;
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
        $this->controller->getView()->title = '美愿基金账单未还记录';
        $searchModel = new MyBillSearch();
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