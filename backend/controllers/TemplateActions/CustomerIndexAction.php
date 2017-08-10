<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/9
 * Time: 上午11:53
 */

namespace backend\controllers\TemplateActions;


use backend\business\AuthorizerUtil;
use backend\business\WeChatUserUtil;
use backend\models\CustomerSearch;
use yii\base\Action;

class CustomerIndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '客服消息';
        $cache = WeChatUserUtil::getCacheInfo();
        $is_verify = AuthorizerUtil::isVerify($cache['verify_type_info']);
        $searchModel = new CustomerSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('customer',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider,
            'is_verify'=>$is_verify
        ]);
    }
}