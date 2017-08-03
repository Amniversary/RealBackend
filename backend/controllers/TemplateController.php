<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/1
 * Time: 上午11:39
 */

namespace backend\controllers;


use yii\web\Controller;

class TemplateController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return require (__DIR__.'/TemplateActions/TemplateConfig.php');
    }

    public function behaviors()
    {
        return require (__DIR__.'/TemplateActions/TemplateBehaviors.php');
    }
}