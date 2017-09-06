<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/5
 * Time: 下午4:21
 */

namespace backend\controllers;




use yii\web\Controller;

class TagController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return require (__DIR__.'/TagActions/TagConfig.php');
    }

    public function behaviors()
    {
        return require (__DIR__.'/TagActions/TagBehaviors.php');
    }
}