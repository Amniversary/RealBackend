<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/2/29
 * Time: 16:48
 */

namespace console\controllers\BeanstalkActions\WorkerActions;

use common\components\SystemParamsUtil;
use common\components\UsualFunForStringHelper;
use common\components\WaterNumUtil;
use common\models\Job;
use common\models\Reward;
use frontend\business\ClientActiveUtil;
use frontend\business\LivingHotUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ChangeClientExperience;
use frontend\business\JobUtil;
use frontend\business\RewardUtil;
use \frontend\business\SaveRecordByransactions\SaveByTransaction\ChangeLivingHotUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateExperienceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ExperienceModifyByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\LivingHotModifyByTrans;
use udokmeci\yii2beanstalk\BeanstalkController;
use yii\base\Action;
use yii\helpers\Console;
use yii\log\Logger;

/**
 * hlq 处理主播每月、每周、每日收入统计、处理热门、经验
 * Class LivingEnterQuitAction
 */
class UserRewardAction extends Action
{
    public function run($job)
    {
        $jobId = $job->getId();
        $sentData = $job->getData();
        $new_job_id =  UsualFunForStringHelper::CreateGUID();
        fwrite(STDOUT, Console::ansiFormat("user_reward in job id:[$jobId]---[$new_job_id]"."\n", [Console::FG_GREEN]));
        try
        {
            $jobRecord = JobUtil::GetJobById($new_job_id);
            if(!isset($jobRecord))
            {
                if(!JobUtil::AddJobToDb($jobId,$new_job_id,'user_reward',$sentData,$error,$jobRecord))
                {
                    fwrite(STDOUT, Console::ansiFormat("---user_reward--  error no jobrecord"."\n", [Console::FG_GREEN]));
                    return BeanstalkController::DELETE;
                }
               // $jobRecord = JobUtil::GetJobById($new_job_id);
            }

            //经验处理
            //账户活跃信息表
            $clentActive = ClientActiveUtil::GetClientActiveInfoByUserId($sentData->user_id);
            $to_experience = SystemParamsUtil::GetSystemParam('living_bean_to_experience',false,'value1'); //豆与经验转化率
            $experience_num = $sentData->gift_value*$to_experience; //当前经验值
//            $rewardUtil = RewardUtil::GetRewardById($sentData->reward_id);

//            $hot_info = LivingHotUtil::GetLivingHotByLivingId($sentData->living_id);

            //创建经验日志参数
            $extend_params = [
                'device_type' => $sentData->device_type,
                'user_id' => $sentData->user_id,
                'source_type' => 1, //送礼物
                'living_before_id' => $sentData->living_before_id,
                'change_rate' => $to_experience,
                'experience' => $experience_num,
                'create_time' => date('Y-m-d H:i:s'),
                'gift_value' => $sentData->gift_value,
                'relate_id' => $sentData->reward_id,
            ];

            $transActions = [];

//            $transActions[] = new LivingHotModifyByTrans($hot_info,['living_id'=>$sentData->living_id]);
            if($experience_num > 0)
            {
                $transActions[] = new ExperienceModifyByTrans($clentActive,['experience_num'=>$experience_num]);
            }

            $transActions[] = new CreateExperienceLogByTrans($clentActive,$extend_params);
            $rewardUtil = new Reward();
            $rewardUtil->living_master_id = $sentData->living_master_id;
            $rewardUtil->gift_value = $sentData->gift_value;
            $transActions[] = new ChangeLivingHotUtil($rewardUtil,['money_type'=>$sentData->money_type]);
            if(!RewardUtil::RewardSaveByTransaction($transActions,$outInfo, $error))
            {
                $error1  = $error;
                fwrite(STDOUT, Console::ansiFormat("user_reward in 22222"."\n", [Console::FG_GREEN]));
                if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                {
                    $error = iconv('utf-8','gb2312',$error);
                }
                fwrite(STDOUT, Console::ansiFormat("---user_reward--  error:$error"."\n", [Console::FG_GREEN]));
                $jobRecord->remark1 = $error1;
                $jobRecord->status = 2;
                \Yii::getLogger()->log('任务处理失败，jobid：'.$jobId.'----'.$new_job_id.' :'.$error,Logger::LEVEL_ERROR);
                fwrite(STDOUT, Console::ansiFormat("user_reward in 1111"."\n", [Console::FG_GREEN]));
                if(!$jobRecord->save())
                {
                    \Yii::getLogger()->log('保存任务状态失败2 ：'.var_export($jobRecord->getErrors(),true),Logger::LEVEL_ERROR);
                }
                return BeanstalkController::DELETE;
            }
            fwrite(STDOUT, Console::ansiFormat("user_reward in 222f2222222ddsf22"."\n", [Console::FG_GREEN]));
            $jobRecord->status = 4;
            if(!$jobRecord->save())
            {
                \Yii::getLogger()->log('保存任务状态失败4 ：'.var_export($jobRecord->getErrors(),true),Logger::LEVEL_ERROR);
            }

            $everthingIsAllRight =true;
            if($everthingIsAllRight == true){
                fwrite(STDOUT, Console::ansiFormat("---user_reward--  Everything is allright"."\n", [Console::FG_GREEN]));
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