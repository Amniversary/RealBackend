<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/21
 * Time: 下午5:15
 */

namespace frontend\controllers;


use yii\web\Controller;

class WeixinController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return require(__DIR__.'/WeiXinActions/WeiXinActionConfig.php');
    }

    public function behaviors()
    {
        return require(__DIR__.'/WeiXinActions/WeiXinBehaviors.php');
    }
}