<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/27
 * Time: 9:37
 */

namespace console\controllers\ImActions\WorkerActions;


use common\components\UsualFunForStringHelper;
use common\models\Job;
use frontend\business\AttentionUtil;
use frontend\business\JobUtil;
use frontend\business\SendGiftIm;
use frontend\business\SendImUtil;
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
        //$new_job_id =  UsualFunForStringHelper::CreateGUID();
        fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')."---$sentData->key_word-- in job id:[$jobId]---"."\n", [Console::FG_GREEN]));
        try {


            if(!SendImUtil::GetSendImParams($sentData,$sentData->key_word,$error))
            {
                $error1 = $error;
                if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                {
                    $error = iconv('utf-8','gb2312',$error);
                }
                fwrite(STDOUT, Console::ansiFormat(" --$sentData->key_word-- $error no jobrecord "."\n", [Console::FG_GREEN]));
                \Yii::getLogger()->log('任务处理失败，jobid：'.$jobId.'--- :'.$error,Logger::LEVEL_ERROR);

                return BeanstalkController::DELETE;
            }


            $everthingIsAllRight = true;
            if($everthingIsAllRight == true){
                fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')." ---$sentData->key_word--  Everything is allright"."\n", [Console::FG_GREEN]));
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