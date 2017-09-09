<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/9
 * Time: 下午6:01
 */

namespace backend\controllers\ArticleActions;


use backend\business\KeywordUtil;
use yii\base\Action;

class CreateAction extends Action
{
    public function run()
    {
        $selection = KeywordUtil::GetOrderAuth();//TODO:公众号已有配置
        $rights = KeywordUtil::GetAuthParams();//TODO: 配置列表
        $this->controller->layout='main_empty';
        return $this->controller->render('order_auth',[
            'rights'=>$rights,
            'selections' =>$selection
        ]);
    }
}