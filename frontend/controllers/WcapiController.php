<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/10
 * Time: 下午4:36
 */

namespace frontend\controllers;


use yii\filters\Cors;
use yii\web\Controller;

class WcapiController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:*');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        return require(__DIR__.'/WcapiActions/WcapiActionConfig.php');
    }

    public function behaviors()
    {
        return require(__DIR__.'/WcapiActions/WcapiBehaviors.php');
    }
}