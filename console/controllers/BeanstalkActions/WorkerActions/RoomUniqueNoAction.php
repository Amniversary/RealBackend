<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/2/29
 * Time: 16:48
 */

namespace console\controllers\BeanstalkActions\WorkerActions;

use frontend\business\JobUtil;
use udokmeci\yii2beanstalk\BeanstalkController;
use yii\base\Action;
use yii\helpers\Console;
use yii\log\Logger;

class RoomUniqueNoAction extends Action
{
    public function run($job)
    {
        $sentData = $job->getData();
        $jobId = $job->getId();
        fwrite(STDOUT, Console::ansiFormat("---tube-- in job id:[$jobId]----"."\n", [Console::FG_GREEN]));
        try {
            // something useful here
            $sqlTemplate = 'insert into mb_room_no_list(room_no,status,is_use)values';
            $sql = $sqlTemplate;
            $sql .= sprintf('(%s,1,0);',$sentData->room_no);
            $rst = \Yii::$app->db->createCommand($sql)->execute();
            if($rst < 0)
            {
                \Yii::getLogger()->log('错误：'.$sql,Logger::LEVEL_ERROR);
            }


           /* $count = 0;
            for($i =0; $i < $end; $i ++)
            {
                if($i > 0 && $i % 10000 === 0)
                {
                    $sql = substr($sql,0,strlen($sql) -1);
                    $rst = \Yii::$app->db->createCommand($sql)->execute();
                    $count ++;
                    echo strval($count).' '. date('Y-m-d H:i:s').' '.$rst."\n";
                    $sql = $sqlTemplate;
                }
                $sql .= sprintf('(%s,1,0),',$codes[$i]);
            }
            if($i % 10000 !== 0)
            {
                $sql = substr($sql,0,strlen($sql) -1);
                $rst = \Yii::$app->db->createCommand($sql)->execute();
                $count ++;
                echo strval($count).' '. date('Y-m-d H:i:s').' '.$rst."\n";
            }*/
            $everthingIsAllRight = true;
            if($everthingIsAllRight == true){
                fwrite(STDOUT, Console::ansiFormat("---unique_no--  Everything is allright"."\n", [Console::FG_GREEN]));
                //Delete the job from beanstalkd
                return BeanstalkController::DELETE;
            }

            $everthingWillBeAllRight = false;
            if($everthingWillBeAllRight == true){
                fwrite(STDOUT, Console::ansiFormat("- Everything will be allright"."\n", [Console::FG_GREEN]));
                //Delay the for later try
                //You may prefer decay to avoid endless loop
                return BeanstalkController::DELAY;
            }

            $IWantSomethingCustom = false;
            if($IWantSomethingCustom==true){
                \Yii::$app->beanstalk->release($job);
                return BeanstalkController::NO_ACTION;
            }

            fwrite(STDOUT, Console::ansiFormat("- Not everything is allright!!!"."\n", [Console::FG_GREEN]));
            //Decay the job to try DELAY_MAX times.
            return BeanstalkController::DECAY;

            // if you return anything else job is burried.
        } catch (\Exception $e) {
            //If there is anything to do.
            fwrite(STDERR, Console::ansiFormat($e."\n", [Console::FG_RED]));
            // you can also bury jobs to examine later
            return BeanstalkController::BURY;
        }
    }
} 