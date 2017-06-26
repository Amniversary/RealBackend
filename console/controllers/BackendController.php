<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/11
 * Time: 9:27
 */

namespace console\controllers;


use yii\console\Controller;

class BackendController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return require(__DIR__.'/BackendActions/BackendConfig.php');
    }

    public function behaviors()
    {
        return require(__DIR__.'/BackendActions/BackendBehaviors.php');
    }
} 