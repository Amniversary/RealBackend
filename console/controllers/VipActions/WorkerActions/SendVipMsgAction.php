<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/12
 * Time: 上午11:23
 */

namespace console\controllers\VipActions\WorkerActions;


use backend\business\AuthorizerUtil;
use backend\business\WeChatUserUtil;
use udokmeci\yii2beanstalk\BeanstalkController;
use yii\base\Action;
use yii\helpers\Console;

class SendVipMsgAction extends Action
{
    public function run($job)
    {
        $jobId = $job->getId();
        $sentData = $job->getData();
        fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')."---$sentData->key_word-- in job id:[$jobId]---"."\n", [Console::FG_GREEN]));
        try {
            sleep(2);
            $openid = $sentData->open_id;
            $item = json_decode(json_encode($sentData->item),true);
            $json = WeChatUserUtil::getMsgTemplate($item,$openid);
            $rst = WeChatUserUtil::sendCustomerMsg($sentData->accessToken,$json);
            fwrite(STDOUT, Console::ansiFormat("json: $json "."\n", [Console::FG_GREEN]));
            if($rst['errcode'] != 0)
            {
                $error = 'Code:'. $rst['errcode']. ' Msg:'.$rst['errmsg'];
                if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $error = iconv('utf-8','gb2312',$error);
                }
                fwrite(STDOUT, Console::ansiFormat(" --$sentData->key_word- $json-accessToken: $sentData->authorizer_access_token --- $error no jobrecord "."\n", [Console::FG_GREEN]));
                \Yii::getLogger()->log('任务处理失败，jobid：'.$jobId.' rst : '.var_export($rst,true) .'-accessToken : '. $sentData->authorizer_access_token.'--'.$json .' :'.$error,Logger::LEVEL_ERROR);
                \Yii::getLogger()->flush(true);
                if(\Yii::$app->params['is_alarm'] == 1) {
                    AuthorizerUtil::isAlarms($rst, $sentData->app_id, '成为Vip消息');
                }
                return BeanstalkController::DELETE;
            }
            fwrite(STDOUT, Console::ansiFormat("rst: $rst "."\n", [Console::FG_GREEN]));

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