<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/24
 * Time: 下午4:06
 */

namespace console\controllers\TemplateActions\WorkerActions;


use backend\business\AuthorizerUtil;
use backend\business\WeChatUserUtil;
use common\models\TemplateTiming;
use udokmeci\yii2beanstalk\BeanstalkController;
use yii\base\Action;
use yii\db\Query;
use yii\helpers\Console;
use yii\log\Logger;

class SendBatchUserMsgAction extends Action
{
    public function run($job)
    {
        $jobId = $job->getId();
        $sentData = $job->getData();
        fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')."---$sentData->key_word-- in job id:[$jobId]---"."\n", [Console::FG_GREEN]));
        try {
            set_time_limit(0);
            $auth = AuthorizerUtil::getAuthByOne($sentData->app_id);
            $type = $sentData->type; //TODO: type 3 测试消息  4 群发消息
            $accessToken = $auth->authorizer_access_token;
            $data = json_decode(json_encode($sentData->data),true);
            $query = [];
            if($type ==  3) {
                $openid = $data['openid'];
                if(empty($openid)) {
                    fwrite(STDOUT, Console::ansiFormat("test openid is null :" . $openid."  app_id : ".$auth->record_id."\n",[Console::FG_GREEN]));
                    return BeanstalkController::DELETE;
                }
                $User = AuthorizerUtil::getUserForOpenId($openid, $auth->record_id);
                $query[] = ['client_id'=>$User->client_id,'open_id'=>$User->open_id,'nick_name'=>$User->nick_name,'app_id'=>$User->app_id];
                unset($data['openid']);
            }else if($type == 4){
                $query = (new Query())
                    ->select(['client_id','open_id','nick_name','app_id'])
                    ->from('wc_client')
                    ->where('app_id = :appid and subscribe = :sub',[':appid'=>$auth->record_id,':sub'=>1])
                    ->all();
            }
            $count = count($query);
            $i = 0;
            foreach($query as $list) {
                $json = WeChatUserUtil::getMsgTemplate($data, $list['open_id']);
                $rst = WeChatUserUtil::sendCustomerMsg($accessToken,$json);
                if($rst['errcode'] != 0 || !$rst) {
                    $error = $rst;
                    if($rst['errcode'] == 40001 || $rst['errcode'] == 42001) {
                        $auth = AuthorizerUtil::getAuthByOne($sentData->app_id);
                        $accessToken = $auth->authorizer_access_token;
                    }
                    if($rst['errcode'] == 45015) continue;
                    if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                        $error = iconv('utf-8','gb2312',$error);
                    fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')."发送客服消息失败:  nick_name : ".$list['nick_name']." openId :" . $list['open_id']."  app_id : ".$auth->record_id."  app_name : ".$auth->nick_name."\n",[Console::FG_GREEN]));
                    fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')."Code :".$rst['errcode']. ' msg :'.$rst['errmsg'] ."\n",[Console::FG_GREEN]));
                    \Yii::getLogger()->log('任务处理失败，jobid：'.$jobId.' -- :'.var_export($error,true) .'  openId :'.$sentData->open_id .' ',Logger::LEVEL_ERROR);
                    \Yii::getLogger()->flush(true);
                    if(\Yii::$app->params['is_alarm'] == 1) {
                        if (!AuthorizerUtil::isAlarms($rst, $sentData->app_id, 'Template-发送消息')) break;
                    }
                    continue;
                }
                $i ++;
                fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')." --nick_name : ".$list['nick_name'] ." -- openId :".$list['open_id']. " appId :".$auth->record_id ."  app_name :". $auth->nick_name."\n", [Console::FG_GREEN]));
                fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')." --".json_encode($rst)."--$sentData->key_word--  Everything is allright"."\n", [Console::FG_GREEN]));
            }
            fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')." 消息数 $count ;--发送成功 $i --$sentData->key_word--任务执行完成!"."\n", [Console::FG_GREEN]));
            return BeanstalkController::DELETE;
        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e->getMessage()."\n", [Console::FG_RED]));
            return BeanstalkController::DELETE;
        }
    }
}