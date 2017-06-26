<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/12
 * Time: 16:11
 */

namespace frontend\controllers;


use yii\web\Controller;

class MybackController extends Controller
{

    public $enableCsrfValidation = false;

    public function actions()
    {
        return require(__DIR__.'/MybackActions/MybackActionConfig.php');
    }

    public function behaviors()
    {
        return require(__DIR__.'/MybackActions/MybackBehaviors.php');
    }
} 