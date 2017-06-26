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


class DecreaseAction extends Action
{
	public function run()
    {
        $this->controller->getView()->title = '减少余额';
        $searchModel = new BalanceManageSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
		return $this->controller->render('decrease',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
		    ]
        );
	}	
}
