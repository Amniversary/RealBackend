<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/11
 * Time: 下午2:23
 */

namespace console\controllers;


use udokmeci\yii2beanstalk\BeanstalkController;

class WorkergetuserController extends BeanstalkController
{
    const DELAY_TIME = 1;
    const DELAY_MAX = 3;

    public function init()
    {
        $this->beanstalk = \Yii::$app->getUserBeanstalk;
        parent::init(); // TODO: Change the autogenerated stub
    }

    public function listenTubes()
    {
        return require(__DIR__ . '/GetUserActions/ListenTubesConfig.php');
    }

    public function actions()
    {
        return require  (__DIR__ . '/GetUserActions/GetUserConfig.php');
    }
}