<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/21
 * Time: 下午5:51
 */

namespace backend\controllers;


use yii\web\Controller;

class UsermanageController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return require(__DIR__.'/UserManageActions/UserManageActionsConfig.php');
    }

    public function behaviors()
    {
        return require(__DIR__.'/UserManageActions/UserManageBehaviors.php');
    }
}