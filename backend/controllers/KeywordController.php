<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/4
 * Time: 下午1:27
 */

namespace backend\controllers;


use yii\web\Controller;

class KeywordController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return require (__DIR__.'/KeyWordActions/KeyWordConfig.php');
    }


    /*public function behaviors()
    {
        return require (__DIR__.'/KeyWordActions/KeyWordBehaviors.php');
    }*/
}