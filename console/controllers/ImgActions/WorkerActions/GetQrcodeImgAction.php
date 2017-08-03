<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/20
 * Time: 下午5:47
 */

namespace console\controllers\ImgActions\WorkerActions;


use backend\components\WeChatClass\EventClass;
use udokmeci\yii2beanstalk\BeanstalkController;
use yii\base\Action;
use yii\helpers\Console;
use yii\log\Logger;

class GetQrcodeImgAction extends Action
{
    public function run($job)
    {
        $jobId = $job->getId();
        $sentData = $job->getData();
        fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')."---$sentData->key_word-- in job id:[$jobId]---"."\n", [Console::FG_GREEN]));
        try {
            $arr = json_decode(json_encode($sentData->data),true);
            $Event = new EventClass($arr);
            if(!$Event->getQrCodeImg($error))
            {
                if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                {
                    $error = iconv('utf-8','gb2312',$error);
                }
                fwrite(STDOUT, Console::ansiFormat(" --$sentData->key_word-- $error -- openId : ".$arr['FromUserName']." AppId:". $arr['appid']."no jobrecord "."\n", [Console::FG_GREEN]));
                \Yii::getLogger()->log('任务处理失败，jobid：'.$jobId.'--- :'.$error .' openId :'.$arr['FromUserName'] . ' AppId : '. $arr['appid'],Logger::LEVEL_ERROR);
                \Yii::getLogger()->flush(true);
                return BeanstalkController::DELETE;
            }
            fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')." ---$sentData->key_word--  Everything is allright"."\n", [Console::FG_GREEN]));
            return BeanstalkController::DELETE;
        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e->getMessage()."\n", [Console::FG_RED]));
            return BeanstalkController::DELETE;
        }
    }
}