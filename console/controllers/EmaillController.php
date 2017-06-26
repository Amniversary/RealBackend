<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/30
 * Time: 17:22
 */

namespace console\controllers;


use yii\base\Exception;
use yii\console\Controller;
use yii\log\Logger;

class EmaillController extends Controller
{
    public function actionIndex($log_num)
    {
        //群发邮件
        set_time_limit(0);
        ini_set ('memory_limit', '1024M');
        $fileName = \Yii::$app->getBasePath().'/runtime/api_logs/'.$log_num.'_api_log_'.date('Y-m-d',strtotime('-1 day')).'.tar.bz2';//tar.gz
        //$jobName = \Yii::$app->getBasePath().'/runtime/mb_logs/mb_job_log_'.date('Y-m-d',strtotime('-1 day')).'.tar.gz';
        if(!file_exists($fileName))
        {
            $error = " $log_num log file does not exist!\n";
            echo $error;
            \Yii::getLogger()->log($error.': '.$fileName,Logger::LEVEL_ERROR);
            return false;
        }

        $email = ['hebihan@mblive.cn','zhoujiaman@mblive.cn'];
        $messages = [];
        $messages[] = \Yii::$app->mailer->compose('test-html')
            ->setCharset('UTF-8')
            ->setSubject($log_num.'-Api-Log日志文件-'.date('Y-m-d'))
            ->attach($fileName)
            //->attach($jobName)
            ->setTo($email);
        $success_num = \Yii::$app->mailer->sendMultiple($messages);

        if($success_num == 0)
        {
            $error = " $log_num log send mail failed!\n";
            echo $error;
            \Yii::getLogger()->log($error,Logger::LEVEL_ERROR);
        }
        if($success_num > 0)
        {
            $shell = 'rm -f '.$fileName;
            shell_exec($shell);
        }
        $send_mail_file = \Yii::$app->getBasePath().'/runtime/api_logs/send_mail.log';
        $date = date('Y-m-d');
        file_put_contents($send_mail_file,$date);
        \Yii::getLogger()->log($success_num.'条消息发送成功',Logger::LEVEL_ERROR);
        echo " $log_num log send mail ok!\n";
        return true;
    }

    /**
     * 发送mb_job日志 到管理员邮箱
     * @return bool
     */
    public function actionMbjob()
    {
        //群发邮件
        set_time_limit(0);
        ini_set ('memory_limit', '1024M');
        //$fileName = \Yii::$app->getBasePath().'/runtime/api_logs/api_log_'.date('Y-m-d',strtotime('-1 day')).'.tar.gz';//tar.gz
        $jobName = \Yii::$app->getBasePath().'/runtime/mb_logs/mb_job_log_'.date('Y-m-d',strtotime('-1 day')).'.tar.gz';

        if(!file_exists($jobName))
        {
            $error = " job file does not exist\n";
            echo $error;
            \Yii::getLogger()->log($error.': '.$jobName,Logger::LEVEL_ERROR);
            return false;
        }
        $email = ['hebihan@mblive.cn','linxiaoyi@mblive.cn','helieqiang@mblive.cn','wangdi@mblive.cn'];
        $messages = [];
        $messages[] = \Yii::$app->mailer->compose('test-html')
            ->setCharset('UTF-8')
            ->setSubject('MbJob-Log日志文件-'.date('Y-m-d'))
            //->attach($fileName)
            ->attach($jobName)
            ->setTo($email);
        $success_num = \Yii::$app->mailer->sendMultiple($messages);

        if($success_num == 0)
        {
            $error = " job send mail failed!\n";
            echo $error;
            \Yii::getLogger()->log($error,Logger::LEVEL_ERROR);
            return false;
        }

        $send_mail_file = \Yii::$app->getBasePath().'/runtime/api_logs/send_mail.log';
        $date = date('Y-m-d');
        file_put_contents($send_mail_file,$date);
        \Yii::getLogger()->log($success_num.'条消息发送成功',Logger::LEVEL_ERROR);
        echo " job send mail ok!\n";
        return true;
    }

    public function actionTest($a)
    {
        echo $a."\n";
    }
    /**
     * 缓存随机串
     */
    public function actionCached($randStr)
    {
        if(!isset($randStr))
        {
            echo " ranStr is Null\n";
            return false;
        }

        $len = strlen($randStr);
        if($len < 20)
        {
            echo " ranStr Invalid format\n";
            return false;
        }

        \Yii::$app->cache->set('cache_ranstr',$randStr);
        echo " cached is ok!\n";
        return true;
    }
} 