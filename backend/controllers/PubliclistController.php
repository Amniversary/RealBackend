<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/29
 * Time: 下午12:08
 */

namespace backend\controllers;


use yii\web\Controller;

class PubliclistController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return require (__DIR__.'/PublicListActions/PublicListConfig.php');
    }

    public function behaviors()
    {
        return require (__DIR__.'/PublicListActions/PublicListBehaviors.php');
    }
}