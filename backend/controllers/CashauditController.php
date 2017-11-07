<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/11/3
 * Time: 下午3:59
 */

namespace backend\controllers;


use yii\web\Controller;

class CashauditController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return require (__DIR__.'/CashAuditActions/Config.php');
    }

    public function behaviors()
    {
        return require (__DIR__.'/CashAuditActions/Behaviors.php');
    }
}