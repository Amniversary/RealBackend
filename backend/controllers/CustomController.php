<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/7
 * Time: 下午3:52
 */

namespace backend\controllers;


use yii\web\Controller;

class CustomController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return require (__DIR__.'/CustomActions/CustomConfig.php');
    }

    public function behaviors()
    {
        return require (__DIR__.'/CustomActions/CustomBehaviors.php');
    }
}