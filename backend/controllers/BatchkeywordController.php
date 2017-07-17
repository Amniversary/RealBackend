<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/16
 * Time: 下午10:22
 */

namespace backend\controllers;


use yii\web\Controller;

class BatchkeywordController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return require (__DIR__.'/BatchKeyWordActions/BatchKeyWordConfig.php');
    }

    public function behaviors()
    {
        return require (__DIR__.'/BatchKeyWordActions/BatchKeyWordBehaviors.php');
    }
}