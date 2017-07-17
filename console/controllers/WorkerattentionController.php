<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/13
 * Time: 下午3:43
 */

namespace console\controllers;


use udokmeci\yii2beanstalk\BeanstalkController;

class WorkerattentionController extends BeanstalkController
{
    public function init()
    {
        $this->beanstalk = \Yii::$app->attentionBeanstalk;
        parent::init();
    }
    public function listenTubes()
    {
        return require(__DIR__ . '/AttentionActions/ListenTubesConfig.php');
    }

    public function actions()
    {
        return require(__DIR__ . '/AttentionActions/AttentionBeanstalkConfig.php');
    }
}
