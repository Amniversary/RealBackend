<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/2/29
 * Time: 16:48
 */

namespace console\controllers\BeanstalkActions\WorkerActions;

use common\components\UsualFunForStringHelper;
use common\models\Job;
use frontend\business\AttentionUtil;
use frontend\business\JobUtil;
use frontend\business\LivingUtil;
use udokmeci\yii2beanstalk\BeanstalkController;
use yii\base\Action;
use yii\helpers\Console;
use yii\log\Logger;

/**
 * lxy 退出和进入直播间
 * Class LivingEnterQuitAction
 * @package console\controllers\BeanstalkActions\WorkerActions
 */
class LivingEnterQuitAction extends Action
{
    public function run($job)
    {
        $jobId = $job->getId();
        $sentData = $job->getData();
        $new_job_id =  UsualFunForStringHelper::CreateGUID();
        fwrite(STDOUT, Console::ansiFormat("---living_enter_quit-- in job id:[$jobId]---[$new_job_id]"."\n", [Console::FG_GREEN]));
        try
        {
            $jobRecord = JobUtil::GetJobById($new_job_id);
            if(!isset($jobRecord))
            {
                if(!JobUtil::AddJobToDb($jobId,$new_job_id,'living_enter_quit',$sentData,$error,$jobRecord))
                {
                    fwrite(STDOUT, Console::ansiFormat("---living_enter_quit--  error no jobrecord"."\n", [Console::FG_GREEN]));
                    return BeanstalkController::DELETE;
                }

                //$jobRecord = JobUtil::GetJobById($new_job_id);
            }
            // something useful here
            $sentData = json_encode($sentData);
            $sentData = json_decode($sentData,true);
           if(!LivingUtil::LivingEnterOrQuit($sentData,$error))
           {
               $error1 = $error;
               if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
               {
                   $error = iconv('utf-8','gb2312',$error);
               }
               fwrite(STDOUT, Console::ansiFormat("---living_enter_quit--  error:$error"."\n", [Console::FG_GREEN]));
               $jobRecord->remark1 = $error1;
               $jobRecord->status = 2;
               \Yii::getLogger()->log('任务处理失败，jobid：'.$jobId.'----'.$new_job_id.' :'.$error,Logger::LEVEL_ERROR);
               if(!$jobRecord->save())
               {
                   \Yii::getLogger()->log('保存任务状态失败2 ：'.var_export($jobRecord->getErrors(),true),Logger::LEVEL_ERROR);
               }
                return BeanstalkController::DELETE;
           }
            if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            {
                $error = iconv('utf-8','gb2312',$error);
            }
            $jobRecord->status = 4;
            if(!$jobRecord->save())
            {
                \Yii::getLogger()->log('保存任务状态失败4 ：'.var_export($jobRecord->getErrors(),true),Logger::LEVEL_ERROR);
            }
            $everthingIsAllRight =true;
            if($everthingIsAllRight == true){
                fwrite(STDOUT, Console::ansiFormat("---living_enter_quit-- s_status:". $sentData['source_status'].' living_id:'.$sentData['living_id']."  Everything is allright;imdata:$error"."\n", [Console::FG_GREEN]));
                //Delete the job from beanstalkd
                return BeanstalkController::DELETE;
            }

            $everthingWillBeAllRight = false;
            if($everthingWillBeAllRight == true){
                fwrite(STDOUT, Console::ansiFormat("- Everything will be allright;"."\n", [Console::FG_GREEN]));
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