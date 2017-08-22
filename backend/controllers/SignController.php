<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/21
 * Time: 上午11:15
 */

namespace backend\controllers;


use yii\web\Controller;

class SignController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return require (__DIR__.'/SignActions/SignConfig.php');
    }

    public function behaviors()
    {
        return require (__DIR__.'/SignActions/SignBehaviors.php');
    }
}