<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/2/29
 * Time: 16:48
 */

namespace console\controllers\BeanstalkActions\WorkerActions;

use common\components\tenxunlivingsdk\TimRestApi;
use common\components\UsualFunForStringHelper;
use frontend\business\JobUtil;
use udokmeci\yii2beanstalk\BeanstalkController;
use yii\base\Action;
use yii\helpers\Console;
use yii\log\Logger;

/**
 * hlq 送礼物 发送IM消息
 * Class LivingEnterQuitAction
 */
class LivingSendGiftImInfo extends Action
{
    public function run($job)
    {
        $time1 = microtime(true);
        $jobId = $job->getId();
        $sentData = $job->getData();
        $new_job_id =  UsualFunForStringHelper::CreateGUID();
        fwrite(STDOUT, Console::ansiFormat("send_gift_im in job id:[$jobId]---[$new_job_id]"."\n", [Console::FG_GREEN]));
        try
        {
            $jobRecord = JobUtil::GetJobById($new_job_id);
            if(!isset($jobRecord))
            {
                if(!JobUtil::AddJobToDb($jobId,$new_job_id,'send_gift_im',$sentData,$error,$jobRecord))
                {
                    fwrite(STDOUT, Console::ansiFormat("---send_gift_im--  error no jobrecord"."\n", [Console::FG_GREEN]));
                    return BeanstalkController::DELETE;
                }
            }

            //向群发送消息
            $sendInfo = [
                'type' => 2,
                'tickets_num' => $sentData->tickets_num,
            ];
            $text = json_encode($sendInfo);
            if(!TimRestApi::group_send_group_msg_custom((string)$sentData->user_id,$sentData->other_id,$text,$error))
            {
                \Yii::getLogger()->log('送礼物发送im消息失败：'.'fail:'.$error.' date_time:'.date('Y-m-d H:i:s'),Logger::LEVEL_ERROR);

                $error1  = $error;
                fwrite(STDOUT, Console::ansiFormat("send_gift_im in All Right"."\n", [Console::FG_GREEN]));
                if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                {
                    $error = iconv('utf-8','gb2312',$error);
                }
                fwrite(STDOUT, Console::ansiFormat("---send_gift_im--  error:$error"."\n", [Console::FG_GREEN]));
                $jobRecord->remark1 = $error1;
                $jobRecord->status = 2;
                \Yii::getLogger()->log('送礼物发送im消息失败：，jobid：'.$jobId.'----'.$new_job_id.' :'.$error,Logger::LEVEL_ERROR);
                fwrite(STDOUT, Console::ansiFormat("send_gift_im in 1111"."\n", [Console::FG_GREEN]));
                if(!$jobRecord->save())
                {
                    \Yii::getLogger()->log('送礼物发送im消息失败：2 ：'.var_export($jobRecord->getErrors(),true),Logger::LEVEL_ERROR);
                }
                return BeanstalkController::DELETE;
            }
            fwrite(STDOUT, Console::ansiFormat("send_gift_im in All Right"."\n", [Console::FG_GREEN]));
            $jobRecord->status = 4;
            if(!$jobRecord->save())
            {
                \Yii::getLogger()->log('送礼物发送im消息失败：4 ：'.var_export($jobRecord->getErrors(),true),Logger::LEVEL_ERROR);
            }
            $time2 = microtime(true);
            $alltime = $time2-$time1;
            $everthingIsAllRight =true;
            if($everthingIsAllRight == true){
                fwrite(STDOUT, Console::ansiFormat($alltime."---send_gift_im--  Everything is allright"."\n", [Console::FG_GREEN]));
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