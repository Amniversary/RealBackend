<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/8
 * Time: 下午2:54
 */

namespace console\controllers\TemplateActions\WorkerActions;


use backend\business\AuthorizerUtil;
use backend\business\WeChatUserUtil;
use backend\components\MessageComponent;
use common\models\Alarm;
use common\models\BatchCustomer;
use common\models\CustomerStatistics;
use udokmeci\yii2beanstalk\BeanstalkController;
use yii\base\Action;
use yii\db\Query;
use yii\helpers\Console;
use yii\log\Logger;

class BatchCustomerAction extends Action
{
    public function run($job)
    {
        $jobId = $job->getId();
        $sentData = $job->getData();
        fwrite(STDOUT, Console::ansiFormat("客服消息任务开始 :" . date('Y-m-d H:i:s') . "---$sentData->key_word-- in job id:[$jobId]---" . "\n", [Console::FG_GREEN]));
        try {
            set_time_limit(0);
            $auth = AuthorizerUtil::getAuthByOne($sentData->app_id);
            $accessToken = $auth->authorizer_access_token;
            $data = json_decode(json_encode($sentData->msgData), true);
            $component = ['appid' => $auth->authorizer_appid];
            $msgObj = new MessageComponent($component);
            $msgData = $msgObj->getMessageItem($data);
            $query = (new Query())
                ->select(['client_id', 'open_id', 'nick_name', 'app_id'])
                ->from('wc_client')
                ->where('app_id = :appid and subscribe = :sub', [':appid' => $auth->record_id, ':sub' => 1])
                ->all();
//            $query[] = ['client_id'=>9617, 'open_id'=>'ol_EGvw_V3rXYILgc7QEOVVBrxwg','nick_name'=>'Gavean', 'app_id' => 76];
            $count = count($query);
            $i = 0;
            foreach ($query as $list) {
                foreach($msgData as $msg) {
                    $json = WeChatUserUtil::getMsgTemplate($msg, $list['open_id']);
                    $rst = WeChatUserUtil::sendCustomerMsg($accessToken, $json);
                    if ($rst['errcode'] != 0 || !$rst) {
                        $error = $rst;
                        if ($rst['errcode'] == 40001) {
                            $auth = AuthorizerUtil::getAuthByOne($sentData->app_id);
                            $accessToken = $auth->authorizer_access_token;
                        }
                        if ($rst['errcode'] == 45015) continue;
                        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                            $error = iconv('utf-8', 'gb2312', $error);
                        fwrite(STDOUT, Console::ansiFormat("--$sentData->key_word--" . date('Y-m-d H:i:s') . "  群发客服消息失败:  nick_name : " . $list['nick_name'] . " openId :" . $list['open_id'] . "  app_id : " . $auth->record_id . "  app_name : " . $auth->nick_name . "\n", [Console::FG_RED]));
                        fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s') . "  Code :" . $rst['errcode'] . ' msg :' . $rst['errmsg'] . "\n", [Console::FG_RED]));
                        \Yii::getLogger()->log("--$sentData->key_word--" . '任务处理失败，jobid：' . $jobId . ' -- :' . var_export($error, true) . '  openId :' . $sentData->open_id . ' ', Logger::LEVEL_ERROR);
                        \Yii::getLogger()->flush(true);
                        if(!AuthorizerUtil::isAlarms($rst, $auth->record_id, '群发客服任务消息')) break;
                        continue;
                    }
                }
                $i++;
                fwrite(STDOUT, Console::ansiFormat("--$sentData->key_word--" . date('Y-m-d H:i:s') . " --nick_name : " . $list['nick_name'] . " -- openId :" . $list['open_id'] . " appId :" . $auth->record_id . "  app_name :" . $auth->nick_name . "\n", [Console::FG_GREEN]));
                fwrite(STDOUT, Console::ansiFormat("--$sentData->key_word--" . date('Y-m-d H:i:s') . " --" . json_encode($rst) . "--$sentData->key_word--  Everything is allright" . "\n", [Console::FG_GREEN]));
            }
            $recordData = ['task_id' => $sentData->task_id, 'app_id' => $auth->record_id, 'user_count' => $count, 'user_num' => $i, 'create_time' => time()];
            $record = new CustomerStatistics();
            $record->attributes = $recordData;
            $record->save();
            fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s') . " 消息数 $count ;--发送成功 $i --$sentData->key_word--任务执行完成!" . "\n", [Console::FG_GREEN]));
            return BeanstalkController::DELETE;
        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e->getMessage() . "\n", [Console::FG_RED]));
            return BeanstalkController::DELETE;
        }
    }
}