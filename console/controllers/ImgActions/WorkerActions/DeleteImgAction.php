<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/20
 * Time: 下午6:10
 */

namespace console\controllers\ImgActions\WorkerActions;


use udokmeci\yii2beanstalk\BeanstalkController;
use yii\base\Action;
use yii\helpers\Console;
use yii\log\Logger;

class DeleteImgAction extends Action
{
    public function run($job)
    {
        $jobId = $job->getId();
        $sentData = $job->getData();
        fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')."---$sentData->key_word-- in job id:[$jobId]---"."\n", [Console::FG_GREEN]));
        try {
            if(!unlink($sentData->qrcode_file) && !unlink($sentData->pic_file)) {
                $error = '删除二维码图片资源或用户头像资源失败';
                fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')." $error "."\n",[Console::FG_BLUE]));
                fwrite(STDOUT,Console::ansiFormat("pic :".$sentData->pic_file . " qrcode :". $sentData->qrcode_file,[Console::FG_BLUE]));
                return BeanstalkController::DELETE;
            }
            $everthingIsAllRight = true;
            if($everthingIsAllRight == true){
                fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')." ---$sentData->key_word--  Everything is allright"."\n", [Console::FG_GREEN]));
                return BeanstalkController::DELETE;
            }
        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e->getMessage()."\n", [Console::FG_RED]));
            return BeanstalkController::DELETE;
        }
    }
}