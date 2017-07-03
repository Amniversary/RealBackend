<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/30
 * Time: 下午6:41
 */

namespace backend\controllers\PublicListActions;


use yii\base\Action;

class KeyWordAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '关键字回复';


        return $this->controller->render('keyword');
    }
}