<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/2
 * Time: 下午3:08
 */

namespace console\controllers;


use udokmeci\yii2beanstalk\BeanstalkController;

class WorkerimgController extends BeanstalkController
{
    const DELAY_PRIORITY = "500";

    const DELAY_TIME = 1;

    const DELAY_MAX = 3;
    public function init()
    {
        $this->beanstalk = \Yii::$app->imgBeanstalk;
        parent::init();
    }
    public function listenTubes()
    {
        return require(__DIR__ . '/ImgActions/ListenTubesConfig.php');
    }

    public function actions()
    {
        return require(__DIR__ . '/ImgActions/ImgBeanstalkConfig.php');
    }
}