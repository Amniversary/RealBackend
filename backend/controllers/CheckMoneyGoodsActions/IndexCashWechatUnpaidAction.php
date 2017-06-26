<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

namespace backend\controllers\CheckMoneyGoodsActions;


use backend\models\CheckMoneyGoodsSearch;
use yii\base\Action;

/**
 * 微信未打款列表
 * Class IndexCashWechatUnpaidAction
 * @package backend\controllers\CheckMoneyGoodsActions
 */
class IndexCashWechatUnpaidAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '微信未打款管理';
        $searchModel = new CheckMoneyGoodsSearch();
        $dataProvider = $searchModel->wechat_unpaid(\Yii::$app->request->queryParams);
        return $this->controller->render('index_wechat_list',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'data_type' => 'unpaid',
            ]
        );


    }
}