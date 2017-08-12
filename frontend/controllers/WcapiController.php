<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/10
 * Time: 下午4:36
 */

namespace frontend\controllers;


use yii\web\Controller;

class WcapiController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return require(__DIR__.'/WcapiActions/WcapiActionConfig.php');
    }

    public function behaviors()
    {
        return require(__DIR__.'/WcapiActions/WcapiBehaviors.php');
    }
}