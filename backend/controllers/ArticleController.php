<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/28
 * Time: 下午1:57
 */

namespace backend\controllers;


use yii\web\Controller;

class ArticleController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return require (__DIR__.'/ArticleActions/ArticleConfig.php');
    }

    public function behaviors()
    {
        return require (__DIR__.'/ArticleActions/ArticleBehaviors.php');
    }
}