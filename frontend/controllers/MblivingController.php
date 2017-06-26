<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/3
 * Time: 9:54
 */
namespace frontend\controllers;

use yii\web\Controller;

class MblivingController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return require(__DIR__.'/MblivingActions/MblivingActionConfig.php');
    }

    public function behaviors()
    {
        return require(__DIR__.'/MblivingActions/MblivingBehaviors.php');
    }
}