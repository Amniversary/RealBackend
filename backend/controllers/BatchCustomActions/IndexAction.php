<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/27
 * Time: 上午11:27
 */

namespace backend\controllers\BatchCustomActions;


use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '批量自定义菜单';

    }

}