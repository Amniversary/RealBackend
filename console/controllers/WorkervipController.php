<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/12
 * Time: 上午11:20
 */

namespace console\controllers;


use udokmeci\yii2beanstalk\BeanstalkController;

class WorkervipController extends BeanstalkController
{
    const DELAY_PRIORITY = "500";

    const DELAY_TIME = 1;

    const DELAY_MAX = 3;
    public function init()
    {
        $this->beanstalk = \Yii::$app->vipBeanstalk;
        parent::init();
    }
    public function listenTubes()
    {
        return require(__DIR__ . '/VipActions/ListenTubesConfig.php');
    }

    public function actions()
    {
        return require(__DIR__ . '/VipActions/VipBeanstalkConfig.php');
    }
}