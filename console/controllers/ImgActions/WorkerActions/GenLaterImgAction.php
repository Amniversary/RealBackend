<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/22
 * Time: 下午7:54
 */

namespace console\controllers\ImgActions\WorkerActions;


use backend\components\MessageComponent;
use udokmeci\yii2beanstalk\BeanstalkController;
use yii\base\Action;
use yii\helpers\Console;
use yii\log\Logger;

class GenLaterImgAction extends Action
{
    public function run($job)
    {
        $jobId = $job->getId();
        $sentData = $job->getData();
        fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')."---$sentData->key_word-- in job id:[$jobId]---"."\n", [Console::FG_GREEN]));
        try {
            $start = microtime(true);
            $data = json_decode(json_encode($sentData->data),true);
            $msgObj = new MessageComponent($data);
            $msgObj->key = $sentData->key;
            $rst = $msgObj->setLaterFlag($error);
            if(!$rst) {
                if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $error = iconv('utf-8','gb2312',$error);
                }
                fwrite(STDOUT, Console::ansiFormat(" --$sentData->key_word-- $error -- openId : ".$data['FromUserName']." AppId:". $data['appid']."no jobrecord "."\n", [Console::FG_GREEN]));
                \Yii::getLogger()->log('任务处理失败，jobid：'.$jobId.'--- :'.$error .' openId :'.$data['FromUserName'] . ' AppId : '. $data['appid'],Logger::LEVEL_ERROR);
                \Yii::getLogger()->flush(true);
                return BeanstalkController::DELETE;
            }
            $msgObj->sendMessageCustom($rst, $data['FromUserName']);
            fwrite(STDOUT, Console::ansiFormat("任务结束时间 Time : ".(microtime(true) - $start)."\n", [Console::FG_GREEN]));
            fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')." ---$sentData->key_word--  Everything is allright"."\n", [Console::FG_GREEN]));
            return BeanstalkController::DELETE;
        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e->getMessage()."\n", [Console::FG_RED]));
            return BeanstalkController::DELETE;
        }
    }
}