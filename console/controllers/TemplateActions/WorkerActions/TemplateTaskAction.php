<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/17
 * Time: 上午10:14
 */

namespace console\controllers\TemplateActions\WorkerActions;


use backend\business\AuthorizerUtil;
use backend\business\TemplateUtil;
use backend\components\TemplateComponent;
use common\models\TemplateTiming;
use udokmeci\yii2beanstalk\BeanstalkController;
use yii\base\Action;
use yii\db\Query;
use yii\helpers\Console;
use yii\log\Logger;

class TemplateTaskAction extends Action
{
    public function run($job)
    {
        $jobId = $job->getId();
        $sentData = $job->getData();
        fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')."---$sentData->key_word-- in job id:[$jobId]---"."\n", [Console::FG_GREEN]));
        try {
            set_time_limit(0);
            $auth = AuthorizerUtil::getAuthByOne($sentData->app_id);
            $type = $sentData->type; //TODO: type 1 测试消息  2 群发消息
            $accessToken = $auth->authorizer_access_token;
            $task = TemplateTiming::findOne(['id'=>$sentData->task_id]);
            $templateData = TemplateUtil::GetTemplateById($sentData->id);
            $template = new TemplateComponent(null,$accessToken);
            $data = json_decode(json_encode($sentData->data),true);
            $query = [];
            if($type ==  1) {
                $openid = $data['openid'];
                if(empty($openid)) {
                    fwrite(STDOUT, Console::ansiFormat("test openid is null :" . $openid."  app_id : ".$auth->record_id."\n",[Console::FG_GREEN]));
                    return BeanstalkController::DELETE;
                }
                $User = AuthorizerUtil::getUserForOpenId($openid, $auth->record_id);
                $query[] = ['client_id'=>$User->client_id,'open_id'=>$User->open_id,'nick_name'=>$User->nick_name,'app_id'=>$User->app_id];
            }else if($type == 2){
                $query = (new Query())
                    ->select(['client_id','open_id','nick_name','app_id'])
                    ->from('wc_client')
                    ->where('app_id = :appid and subscribe = :sub',[':appid'=>$auth->record_id,':sub'=>1])
                    ->all();
            }
            $url = $data['url'];
            unset($data['url']);
            unset($data['openid']);
            foreach($query as $list) {
                $msgData = [];
                foreach($data as $key => $v) {
                    $value = str_replace('{{NICKNAME}}', $list['nick_name'], $v['value']);
                    $msgData[$key] = ['value'=>$value, 'color'=> $v['color']];
                }
                $sendData = $template->BuildTemplate($list['open_id'],$templateData->template_id,$msgData,$url);
                $res = $template->SendTemplateMessage($sendData);
                if($res['errcode'] != 0 || !$res) {
                    $error = $res;
                    if($res['errcode'] == 40001) {
                        $auth = AuthorizerUtil::getAuthByOne($sentData->app_id);
                        $template->accessToken = $auth->authorizer_access_token;
                    }
                    if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                        $error = iconv('utf-8','gb2312',$error);
                    fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')."发送模板消息失败:  nick_name : ".$list['nick_name']." openId :" . $list['open_id']."  app_id : ".$auth->record_id."\n",[Console::FG_GREEN]));
                    fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')."Code :".$res['errcode']. ' msg :'.$res['errmsg'] . "template_ID :". $templateData->template_id."\n",[Console::FG_GREEN]));
                    \Yii::getLogger()->log('任务处理失败，jobid：'.$jobId.' -- :'.var_export($error,true) .'  openId :'.$sentData->open_id .' ',Logger::LEVEL_ERROR);
                    \Yii::getLogger()->flush(true);
                    if(!AuthorizerUtil::isAlarms($res, $sentData->app_id, '发送模板任务消息')) break;
                    continue;
                }
                fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')." --".json_encode($res)."--$sentData->key_word--  Everything is allright"."\n", [Console::FG_GREEN]));
                fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')." --nick_name : ".$list['nick_name'] ." -- openId :".$list['open_id']. " appId :".$auth->record_id."\n", [Console::FG_GREEN]));
            }

            $task->status = 0;
            $task->save();
            fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')." ----$sentData->key_word--任务执行完成!"."\n", [Console::FG_GREEN]));
            return BeanstalkController::DELETE;
        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e->getMessage()."\n", [Console::FG_RED]));
            return BeanstalkController::DELETE;
        }
    }
}