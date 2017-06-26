<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/12
 * Time: 16:11
 */

namespace frontend\controllers;


use yii\web\Controller;

class FuckController extends Controller
{

    public $enableCsrfValidation = false;

    public function actions()
    {
        return require(__DIR__.'/FuckActions/FuckActionConfig.php');
    }

    public function behaviors()
    {
        return require(__DIR__.'/FuckActions/FuckBehaviors.php');
    }

}