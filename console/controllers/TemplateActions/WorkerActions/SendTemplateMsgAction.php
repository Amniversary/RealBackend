<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/31
 * Time: 下午4:53
 */

namespace console\controllers\TemplateActions\WorkerActions;


use backend\components\TemplateComponent;
use udokmeci\yii2beanstalk\BeanstalkController;
use yii\base\Action;
use yii\helpers\Console;
use yii\log\Logger;

class SendTemplateMsgAction extends Action
{
    public function run($job)
    {
        $jobId = $job->getId();
        $sentData = $job->getData();
        fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')."---$sentData->key_word-- in job id:[$jobId]---"."\n", [Console::FG_GREEN]));
        try {
            $template = new TemplateComponent(null,$sentData->accessToken);
            $msg = json_decode(json_encode($sentData->msg),true);
            $res = $template->SendTemplateMessage($msg);
            if($res['errcode'] != 0 || !$res)
            {
                $error = $res;
                if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                {
                    $error = iconv('utf-8','gb2312',$error);
                }
                fwrite(STDOUT, Console::ansiFormat("发送模板消息失败:  nick_name : ".$sentData->nick_name." openId :" . $sentData->open_id."\n",[Console::FG_GREEN]));
                fwrite(STDOUT, Console::ansiFormat("Code :".$res['errcode']. ' msg :'.$res['errmsg']."\n",[Console::FG_GREEN]));
                \Yii::getLogger()->log('任务处理失败，jobid：'.$jobId.' -- :'.var_export($error,true) .'  openId :'.$sentData->open_id .' ',Logger::LEVEL_ERROR);
                \Yii::getLogger()->flush(true);
                return BeanstalkController::DELETE;
            }else{
                fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')." --".json_encode($res)."--$sentData->key_word--  Everything is allright"."\n", [Console::FG_GREEN]));
                return BeanstalkController::DELETE;
            }


            $everthingWillBeAllRight = false;
            if($everthingWillBeAllRight == true){
                fwrite(STDOUT, Console::ansiFormat("- Everything will be allright"."\n", [Console::FG_GREEN]));
                return BeanstalkController::DELAY;
            }

            $IWantSomethingCustom = false;
            if($IWantSomethingCustom==true){
                \Yii::$app->beanstalk->release($job);
                return BeanstalkController::NO_ACTION;
            }

            fwrite(STDOUT, Console::ansiFormat("- Not everything is allright!!!"."\n", [Console::FG_GREEN]));
            return BeanstalkController::DECAY;

        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e->getMessage()."\n", [Console::FG_RED]));
            return BeanstalkController::DELETE;
        }
    }
}