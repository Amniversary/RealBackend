<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/1
 * Time: 上午11:41
 */

namespace backend\controllers\TemplateActions;

use backend\business\AuthorizerUtil;
use backend\business\WeChatUserUtil;
use backend\models\TemplateSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '模板消息';
        $cache = WeChatUserUtil::getCacheInfo();
        $is_verify = AuthorizerUtil::isVerify($cache['verify_type_info']);
        $searchModel = new TemplateSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',[
            'searchModel'=>$searchModel,
            'dataProvider'=> $dataProvider,
            'is_verify'=>$is_verify
        ]) ;
    }
}