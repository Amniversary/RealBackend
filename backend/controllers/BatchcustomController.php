<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/27
 * Time: 上午10:36
 */

namespace backend\controllers;


use yii\web\Controller;

class BatchCustomController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return require (__DIR__.'/BatchCustomActions/BatchCustomBehaviors.php');
    }

    public function actions()
    {
        return require (__DIR__.'/BatchCustomActions/BatchCustomConfig.php');
    }
}