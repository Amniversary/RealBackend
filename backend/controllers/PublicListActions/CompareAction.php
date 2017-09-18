<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/15
 * Time: 下午5:38
 */

namespace backend\controllers\PublicListActions;


use backend\business\KeywordUtil;

use backend\models\CompareForm;
use yii\base\Action;


class CompareAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '公众号比对';
        $auth = KeywordUtil::GetAuthCompare();
        $model = new CompareForm();
        return $this->controller->render('compare' , [
            'auth' => $auth,
            'model'=>$model
        ]);
    }
}