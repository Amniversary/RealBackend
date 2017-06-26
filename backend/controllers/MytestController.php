<?php
namespace backend\controllers;

use yii\web\Controller;

/**
 * Site controller
 */
class MytestController extends Controller
{
    public $enableCsrfValidation = false;
    public $menu=null;

    public function actionTest()
    {
        //var_dump(\Yii::$app->components);
        print 'ok';
    }

}
