<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/10
 * Time: 9:32
 */
namespace console\controllers;

use frontend\business\JobUtil;

class TestController extends \yii\console\Controller
{
    public function fork_process()
    {
        print_r(\Yii::$app->getBasePath());


        if(!function_exists('pcntl_fork'))
        {
            die('  pcntl module not exits');
        }
        for ($i = 1; $i <= 10; $i++)
        {
            $pid = pcntl_fork();
            if (!$pid)
            {
                $this->subProcess($i);
                exit($i);
            }
            else
            {
                echo 'parent id:'.$pid."\n";
            }
        }
        while(pcntl_waitpid(0,$status) != -1)
        {
            $status = pcntl_wexitstatus($status);
            echo "Child $status completed\n";
        }
        echo 'rst:'.$i."\n";
    }
    public function actionDoit()
    {
        $str = date('Y-m-d H:i:s')."\n";
        echo $str;
    }

    public function subProcess($i)
    {
        echo 'sub process'.$i."\n";
    }

}