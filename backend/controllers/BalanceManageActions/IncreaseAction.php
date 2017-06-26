<?php
/*
 * Created By SublimeText3
 * User: jys
 * Date: 2017/3/21
 * Time: 16:00
 */

namespace backend\controllers\BalanceManageActions;

use backend\models\BalanceManageSearch;
use yii\base\Action;
use yii;


class IncreaseAction extends Action
{
	public function run()
    {
        $this->controller->getView()->title = '增加余额';
        $searchModel = new BalanceManageSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        /*var_dump($searchModel);
        var_dump($dataProvider);exit();*/
		return $this->controller->render('increase',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
		    ]
        );
	}	
}

