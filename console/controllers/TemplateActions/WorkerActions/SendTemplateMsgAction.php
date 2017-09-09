<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/31
 * Time: 下午4:53
 */

namespace console\controllers\TemplateActions\WorkerActions;


use backend\business\AuthorizerUtil;
use backend\business\TemplateUtil;
use backend\components\TemplateComponent;
use udokmeci\yii2beanstalk\BeanstalkController;
use yii\base\Action;
use yii\db\Query;
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
            set_time_limit(0);
            $auth = AuthorizerUtil::getAuthByOne($sentData->app_id);
            $accessToken = $auth->authorizer_access_token;
            $templateData = TemplateUtil::GetTemplateById($sentData->id);
            $template = new TemplateComponent(null,$accessToken);
            $data = json_decode(json_encode($sentData->data),true);
            $query = (new Query())
                ->select(['client_id','open_id','nick_name','app_id'])
                ->from('wc_client')
                ->where('app_id = :appid and subscribe = :sub',[':appid'=>$auth->record_id,':sub'=>1])
                ->all();
            $url = $data['url'];
            unset($data['url']);
            $count = count($query);
            $i = 0;
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
                    fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')."发送模板消息失败:  nick_name : ".$list['nick_name']." openId :" . $list['open_id']."  app_id : ".$auth->record_id. " app_name :" .$auth->nick_name."\n",[Console::FG_GREEN]));
                    fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')."Code :".$res['errcode']. ' msg :'.$res['errmsg'] ."   templateId :" .$templateData->template_id ."\n",[Console::FG_GREEN]));
                    \Yii::getLogger()->log('任务处理失败，jobid：'.$jobId.' -- :'.var_export($error,true) .'  openId :'.$sentData->open_id .' ',Logger::LEVEL_ERROR);
                    \Yii::getLogger()->flush(true);
                    if(!AuthorizerUtil::isAlarms($res, $sentData->app_id, '发送模板消息')) break;
                    continue;
                }
                $i ++ ;
                fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')." --".json_encode($res)."--$sentData->key_word--  Everything is allright"."\n", [Console::FG_GREEN]));
                fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')." --nick_name : ".$list['nick_name'] ." -- openId :".$list['open_id']. " appId :".$auth->record_id . " app_name : " . $auth->nick_name."\n", [Console::FG_GREEN]));
            }
            fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')."  消息数 $count ; --发送成功 $i --$sentData->key_word--任务执行完成!"."\n", [Console::FG_GREEN]));
            return BeanstalkController::DELETE;
        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e->getMessage()."\n", [Console::FG_RED]));
            return BeanstalkController::DELETE;
        }
    }
}