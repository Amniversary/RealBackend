<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/04/27
 * Time: 14:00
 */

namespace console\controllers\BeanstalkActions\WorkerActions;

use common\components\UsualFunForStringHelper;
use common\models\Job;
use common\models\LivingHot;
use frontend\business\AttentionUtil;
use frontend\business\JobUtil;
use frontend\business\LivingHotUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\LivingHotModifyByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\UpdateClickLikeSaveForReward;
use udokmeci\yii2beanstalk\BeanstalkController;
use yii\base\Action;
use yii\helpers\Console;
use yii\log\Logger;

/**
 * 点赞后更新热门直播
 * @auth hlq
 *
 */
class TestBhAction extends Action
{
    public function run($job)
    {
        $jobId = $job->getId();
        $sentData = $job->getData();
        //$new_job_id =  UsualFunForStringHelper::CreateGUID();
        fwrite(STDOUT, Console::ansiFormat("---TestBh-- in job id:[$jobId]------"."\n", [Console::FG_GREEN]));
        try
        {
            //sleep(10);
            /*$pid = pcntl_fork();
            if (!$pid)
            {

                //$this->subProcess($i);
                //exit($i);
                exit;
            }
            else
            {
                echo 'parent id:'.$pid."\n";
            }

            while(pcntl_waitpid(0,$status) != -1)
            {
                $status = pcntl_wexitstatus($status);
                echo "Child $status completed\n";
            }*/

            $everthingIsAllRight =true;
            if($everthingIsAllRight == true){
                fwrite(STDOUT, Console::ansiFormat("---TestBh--  Everything is allright"."\n", [Console::FG_GREEN]));
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
            //Decay the job to try DELAY_MAX times. BURIED
            return BeanstalkController::DECAY;

            // if you return anything else job is burried.
        } catch (\Exception $e) {
            //If there is anything to do.
            fwrite(STDERR, Console::ansiFormat($e->getMessage()."\n", [Console::FG_RED]));
            // you can also bury jobs to examine later
            return BeanstalkController::DELETE;
        }
    }
}