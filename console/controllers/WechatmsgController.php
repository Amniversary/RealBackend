<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/12
 * Time: 下午6:14
 */

namespace console\controllers;


use udokmeci\yii2beanstalk\BeanstalkController;

class WechatmsgController extends BeanstalkController
{
    const DELAY_PRIORITY = "1000";
    const DELAY_TIME = 5;
    const DELAY_MAX = 3;

    public function init()
    {
        $this->beanstalk = \Yii::$app->wechatBeanstalk;
        parent::init();
    }
    public function listenTubes()
    {
        return require(__DIR__ . '/WeChatActions/ListenTubesConfig.php');
    }

    public function actions()
    {
        return require(__DIR__ . '/WeChatActions/WeChatBeanstalkConfig.php');
    }

}