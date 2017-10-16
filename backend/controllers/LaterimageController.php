<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/11
 * Time: 上午11:54
 */

namespace backend\controllers;


use yii\web\Controller;

class LaterimageController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return require (__DIR__.'/LaterActions/LaterConfig.php');
    }

    public function behaviors()
    {
        return require (__DIR__.'/LaterActions/LaterBehaviors.php');
    }
}