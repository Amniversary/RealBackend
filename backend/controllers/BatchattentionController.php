<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/26
 * Time: 上午11:31
 */

namespace backend\controllers;


use yii\web\Controller;

class BatchattentionController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return require (__DIR__.'/BatchAttentionActions/BatchAttentionConfig.php');
    }

    public function behaviors()
    {
        return require (__DIR__.'/BatchAttentionActions/BatchAttentionBehaviors.php');
    }
}