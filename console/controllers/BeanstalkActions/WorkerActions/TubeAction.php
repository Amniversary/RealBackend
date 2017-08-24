<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/2/29
 * Time: 16:48
 */

namespace console\controllers\BeanstalkActions\WorkerActions;

use udokmeci\yii2beanstalk\BeanstalkController;
use yii\base\Action;
use yii\db\Query;
use yii\helpers\Console;
use yii\log\Logger;

class TubeAction extends Action
{
    public function run($job)
    {

        $sentData = $job->getData();
        $jobId = $job->getId();
        fwrite(STDOUT, Console::ansiFormat("---tube-- in job id:[$jobId]----"."\n", [Console::FG_GREEN]));
        try {

            set_time_limit(0);
            $query = (new Query())
                ->select(['nick_name','record_id'])
                ->from('wc_authorization_list')
                ->all();
            foreach($query as $list){
                fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')." ----$sentData->key_word--  Everything is allright"."\n", [Console::FG_GREEN]));
                fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')." --nick_name : ".$list['nick_name'] ."\n", [Console::FG_GREEN]));
                sleep(1);
            }
            // something useful here
            $everthingIsAllRight = true;
            if($everthingIsAllRight == true){
                fwrite(STDOUT, Console::ansiFormat(" ---tube--  Everything is allright"."\n", [Console::FG_GREEN]));
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