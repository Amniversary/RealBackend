<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/27
 * Time: 9:37
 */

namespace console\controllers\BeanstalkActions\WorkerActions;


use common\components\UsualFunForStringHelper;
use common\models\Job;
use frontend\business\AttentionUtil;
use frontend\business\JobUtil;
use udokmeci\yii2beanstalk\BeanstalkController;
use yii\base\Action;
use yii\helpers\Console;
use yii\log\Logger;

/**
 * hbh
 * 注册腾讯云
 * @package console\controllers\BeanstalkActions\WorkerActions
 */
class TencentImAction extends Action
{
    public function run($job)
    {
        $jobId = $job->getId();
        $sentData = $job->getData();
        $new_job_id =  UsualFunForStringHelper::CreateGUID();
        fwrite(STDOUT, Console::ansiFormat("---tencent_im-- in job id:[$jobId]---[$new_job_id]"."\n", [Console::FG_GREEN]));
        try {
            // something useful here
            $jobRecord = JobUtil::GetJobById($new_job_id);
            if(!isset($jobRecord))
            {
                if(!JobUtil::AddJobToDb($jobId,$new_job_id,'tencent_im',$sentData,$error,$jobRecord))
                {
                    fwrite(STDOUT, Console::ansiFormat("  --attention--  error no jobrecord"."\n", [Console::FG_GREEN]));
                    return BeanstalkController::DELETE;
                }
                //$jobRecord = JobUtil::GetJobById($new_job_id);
            }
            // something useful here

            if(!AttentionUtil::BeansTalkLogin($sentData,$error))
            {
                $error1 = $error;
                if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                {
                    $error = iconv('utf-8','gb2312',$error);
                }
                fwrite(STDOUT, Console::ansiFormat(" --attention-- $error no jobrecord "."\n", [Console::FG_GREEN]));
                \Yii::getLogger()->log('任务处理失败，jobid：'.$jobId.'----'.$new_job_id.' :'.$error,Logger::LEVEL_ERROR);
                $jobRecord->remark1 = $error1;
                $jobRecord->status = 2;
                if(!$jobRecord->save())
                {
                    \Yii::getLogger()->log('保存任务状态失败2 ：'.var_export($jobRecord->getErrors(),true),Logger::LEVEL_ERROR);
                }
                return BeanstalkController::DELETE;
            }
            $jobRecord->status = 4;
            if(!$jobRecord->save())
            {
                \Yii::getLogger()->log('保存任务状态失败4 ：'.var_export($jobRecord->getErrors(),true),Logger::LEVEL_ERROR);
            }

            $everthingIsAllRight = true;
            if($everthingIsAllRight == true){
                fwrite(STDOUT, Console::ansiFormat(" ---Tencent_im--  Everything is allright"."\n", [Console::FG_GREEN]));
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
            fwrite(STDERR, Console::ansiFormat($e->getMessage()."\n", [Console::FG_RED]));
            // you can also bury jobs to examine later
            return BeanstalkController::DELETE;
        }
    }

} 