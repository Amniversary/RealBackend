<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/11
 * Time: 9:39
 */

namespace console\controllers\BackendActions;


use console\controllers\BackendActions\BackStatistic\IBackStatistic;
use yii\base\Action;
use yii\log\Logger;

class BackStatisticAction extends Action
{
    public function run()
    {
        set_time_limit(0);
        $error = '';
        $errorAll ='';
        $actions = require(__DIR__.'/BackStatistic/StatisticConfig.php');
        $classPrefix = 'console\controllers\BackendActions\BackStatistic\\';
        $instance = null;
        $outInfo = [];
        foreach($actions as $action)
        {
            $class = $classPrefix.$action;
            if(!class_exists($class))
            {
                $errorAll .= '统计类不存在：'.$class."\n";
                \Yii::getLogger()->log('统计类不存在:'.$class,Logger::LEVEL_ERROR);
                continue;
            }
            $instance = new $class;
            if(!($instance instanceof IBackStatistic))
            {
                $errorAll .= '没有实现IBackStatistic接口:'.$class."\n";
                \Yii::getLogger()->log('没有实现IBackStatistic接口：'.$class,Logger::LEVEL_ERROR);
                continue;
            }
            if(!$instance->ExecuteStatistic([],$outInfo,$error))
            {
                $errorAll .= 'class:'.$class.' '.$error."\n";
                \Yii::getLogger()->log('执行统计异常，class：'.$class.' 具体错误：'.$error,Logger::LEVEL_ERROR);
            }
        }
        $tmp = empty($errorAll)?'':(',has error:'."\n".$errorAll);
        $outInfo = 'ok time:'.date('Y-m-d H:i:s').$tmp ."\n";
        echo $outInfo;
    }
}