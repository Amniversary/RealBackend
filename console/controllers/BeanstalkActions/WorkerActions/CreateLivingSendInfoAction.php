<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/2/29
 * Time: 16:48
 */

namespace console\controllers\BeanstalkActions\WorkerActions;

use common\components\getui\GeTuiUtil;
use common\components\tenxunlivingsdk\TimRestApi;
use common\components\UsualFunForStringHelper;
use common\models\Job;
use frontend\business\AttentionUtil;
use frontend\business\ChatPersonGroupUtil;
use frontend\business\ClientUtil;
use frontend\business\JobUtil;
use udokmeci\yii2beanstalk\BeanstalkController;
use yii\base\Action;
use yii\db\Query;
use yii\helpers\Console;
use yii\log\Logger;

/**
 * hlq 处理主播每月、每周、每日收入统计、处理热门、经验
 * Class LivingEnterQuitAction
 */
class CreateLivingSendInfoAction extends Action
{
    public function run($job)
    {
        $jobId = $job->getId();
        $sentData = $job->getData();
        $new_job_id =  UsualFunForStringHelper::CreateGUID();
        fwrite(STDOUT, Console::ansiFormat("create_living_send_info in job id:[$jobId]---[$new_job_id]"."\n", [Console::FG_GREEN]));
        try
        {
            $jobRecord = JobUtil::GetJobById($new_job_id);
            if(!isset($jobRecord))
            {
                if(!JobUtil::AddJobToDb($jobId,$new_job_id,'create_living_send_info',$sentData,$error,$jobRecord))
                {
                    fwrite(STDOUT, Console::ansiFormat("---create_living_send_info--  error no jobrecord"."\n", [Console::FG_GREEN]));
                    return BeanstalkController::BURY;
                }
//                $jobRecord = JobUtil::GetJobById($new_job_id);
            }

            //加入机器人，不这么加入，换一种高效的方式加入
            //ChatPersonGroupUtil::CreatelLivingAddRebots($sentData->living_id,3,$sentData->user_id,30);

            //创建成功向粉丝发送消息,批量发文本消息接口支持一次性针对最多50个用户进行单发消息；
            //$my_friends_id = AttentionUtil::GetFriendListInfo($sentData->user_id);
            $page = 1;
            $page_size = 50;
            $my_friends_id = AttentionUtil::GetFunForGeTui($sentData->user_id,1,50);
            $fCount = count($my_friends_id);
            while($fCount > 0)
            {
/*                $arr = [
                    'type'=>5,   //创建直播
                    'user_id' => $sentData->user_id,
                    'group_id' => $sentData->group_id,
                    'living_id' => $sentData->living_id,
                    'nick_name' => $sentData->nick_name,
                ];*/
                //穿透消息字符个数限制在255，不存用json格式，只用字符串拼接，节约字符串
                $text_content = '5-'.strval($sentData->user_id).'-'.$sentData->group_id.'-'.strval($sentData->living_id).'-'.$sentData->nick_name;
                //$text_content = json_encode($arr);
                $getui_ids =[];
                foreach($my_friends_id as $key=>$fv)
                {
                   // $client_info = ClientUtil::GetClientById($fv['friend_user_id']);
//                        TimRestApi::openim_batch_sendmsg($account_list,$text_content,$error) //IM发消息
                      $getui_ids[] = $fv['getui_id'];
                }
                $show_content = sprintf('您的好友[%s]正在直播，快去瞅瞅吧！',$sentData->nick_name);
                if(count($getui_ids) > 0)
                {
                    if(!GeTuiUtil::PushListMessage($show_content,$text_content,$getui_ids,$error))  //个推群发，最多50个人
                    {
                        fwrite(STDOUT, Console::ansiFormat("---send getui msg --  error,icds:".var_export($getui_ids,true)."\n", [Console::FG_GREEN]));
                        fwrite(STDOUT, Console::ansiFormat("---send getui msg --  error:$error".' content:'.$text_content."\n", [Console::FG_GREEN]));
                    }
                    else
                    {
                        fwrite(STDOUT, Console::ansiFormat("---send getui msg --  error,icds:".var_export($getui_ids,true)."\n", [Console::FG_GREEN]));
                        fwrite(STDOUT, Console::ansiFormat("---send getui ok -- ".' content:'.$text_content."\n", [Console::FG_GREEN]));
                    }
                }
                $page ++;
                $my_friends_id = AttentionUtil::GetFunForGeTui($sentData->user_id,$page,$page_size);
                $fCount = count($my_friends_id);

//                    $error1  = $error;
//                    fwrite(STDOUT, Console::ansiFormat("create_living_send_info in 22222"."\n", [Console::FG_GREEN]));
//                    if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
//                    {
//                        $error = iconv('utf-8','gb2312',$error);
//                    }
//                    fwrite(STDOUT, Console::ansiFormat("---create_living_send_info--  error:$error"."\n", [Console::FG_GREEN]));
//                    $jobRecord->remark1 = $error1;
//                    $jobRecord->status = 2;
//                    \Yii::getLogger()->log('任务处理失败，jobid：'.$jobId.' :'.$error,Logger::LEVEL_ERROR);
//                    fwrite(STDOUT, Console::ansiFormat("create_living_send_info in 1111"."\n", [Console::FG_GREEN]));
//                    if(!$jobRecord->save())
//                    {
//                        \Yii::getLogger()->log('保存任务状态失败2 ：'.var_export($jobRecord->getErrors(),true),Logger::LEVEL_ERROR);
//                    }
//                    return BeanstalkController::DELETE;


            }


            fwrite(STDOUT, Console::ansiFormat("create_living_send_info in 222f2222222ddsf22"."\n", [Console::FG_GREEN]));
            $jobRecord->status = 4;
            if(!$jobRecord->save())
            {
                \Yii::getLogger()->log('保存任务状态失败4 ：'.var_export($jobRecord->getErrors(),true),Logger::LEVEL_ERROR);
            }

            $everthingIsAllRight =true;
            if($everthingIsAllRight == true){
                fwrite(STDOUT, Console::ansiFormat("---create_living_send_info--  Everything is allright"."\n", [Console::FG_GREEN]));
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
            fwrite(STDERR, Console::ansiFormat($e."\n", [Console::FG_RED]));
            // you can also bury jobs to examine later
            return BeanstalkController::BURY;
        }
    }
}