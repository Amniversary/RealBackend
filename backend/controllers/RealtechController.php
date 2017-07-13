<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/11
 * Time: 上午11:30
 */

namespace backend\controllers;


use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class RealtechController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return require (__DIR__ . '/RealtechActions/RealtechConfig.php');
    }

    public function behaviors()
    {
        return require  (__DIR__ . '/RealtechActions/RealtechBehaviors.php');
    }
}