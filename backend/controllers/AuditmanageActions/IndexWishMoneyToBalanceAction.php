<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:17
 */

namespace backend\controllers\AuditmanageActions;


use backend\models\BusinessCheckForWishMoneyToBalanceSearch;
use yii\base\Action;
/**
 * 愿望金额提现列表
 * Class IndexAction
 * @package backend\controllers\RedPacketsActions
 */
class IndexWishMoneyToBalanceAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '愿望提现审核管理';
        $searchModel = new BusinessCheckForWishMoneyToBalanceSearch();
        $params = \Yii::$app->request->queryParams;
        //$params['BusinessCheckSearch']['status']='0';
        $dataProvider = $searchModel->search($params);
        return $this->controller->render('indexwishmoneytobalance',
                [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]
            );
    }
} 