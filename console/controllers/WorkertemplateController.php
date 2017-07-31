<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/31
 * Time: 下午4:45
 */

namespace console\controllers;


use udokmeci\yii2beanstalk\BeanstalkController;

class WorkertemplateController extends BeanstalkController
{
    const DELAY_PRIORITY = "1000";
    const DELAY_TIME = 5;
    const DELAY_MAX = 3;

    public function init()
    {
        $this->beanstalk = \Yii::$app->templateBeanstalk;
        parent::init();
    }
    public function listenTubes()
    {
        return require(__DIR__ . '/TemplateActions/ListenTubesConfig.php');
    }

    public function actions()
    {
        return require(__DIR__ . '/TemplateActions/WeChatBeanstalkConfig.php');
    }
}