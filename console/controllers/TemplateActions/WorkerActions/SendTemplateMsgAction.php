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
                $this->getSleep();
                $msgData = [];
                foreach($data as $key => $v) {
                    $value = str_replace('{{NICKNAME}}', $list['nick_name'], $v['value']);
                    $msgData[$key] = ['value'=>$value, 'color'=> $v['color']];
                }
                $sendData = $template->BuildTemplate($list['open_id'],$templateData->template_id,$msgData,$url);
                $res = $template->SendTemplateMessage($sendData);
                if($res['errcode'] != 0 || !$res) {
                    $error = $res;
                    if($res['errcode'] == 40001 || $res['errcode'] == 42001) {
                        $auth = AuthorizerUtil::getAuthByOne($sentData->app_id);
                        $template->accessToken = $auth->authorizer_access_token;
                    }
                    if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                        $error = iconv('utf-8','gb2312',$error);
                    fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')."发送模板消息失败:  nick_name : ".$list['nick_name']." openId :" . $list['open_id']."  app_id : ".$auth->record_id. " app_name :" .$auth->nick_name."\n",[Console::BG_RED]));
                    fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')."Code :".$res['errcode']. ' msg :'.$res['errmsg'] ."   templateId :" .$templateData->template_id ."\n",[Console::BG_RED]));
                    \Yii::getLogger()->log('任务处理失败，jobid：'.$jobId.' -- :'.var_export($error,true) .'  openId :'.$sentData->open_id .' ',Logger::LEVEL_ERROR);
                    \Yii::getLogger()->flush(true);
                    if(\Yii::$app->params['is_alarm'] == 1) {
                        if (!AuthorizerUtil::isAlarms($res, $sentData->app_id, '发送模板消息')) break;
                    }
                    continue;
                }
                $i ++ ;
                fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')." --".json_encode($res)."--$sentData->key_word--  Everything is allright"."\n", [Console::BG_BLUE]));
                fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')." --nick_name : ".$list['nick_name'] ." -- openId :".$list['open_id']. " appId :".$auth->record_id . " app_name : " . $auth->nick_name."\n", [Console::BG_BLUE]));
                sleep(2);

            }
            fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s')."  消息数 $count ; --发送成功 $i --$sentData->key_word--任务执行完成!"."\n", [Console::FG_GREEN]));
            return BeanstalkController::DELETE;
        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e->getMessage()."\n", [Console::FG_RED]));
            return BeanstalkController::DELETE;
        }
    }

    /**
     * 获取等待时间
     * //TODO: 模板消息 7点 - 10点 时间段  21点 - 01点
     */
    private function getSleep()
    {
        $hours = date('H');
        if(!in_array($hours, [1, 7, 8, 9, 10, 21, 22, 23, 24])) {
            $now_time = time();
            $time_7 = strtotime('07:00:00');
            $time_21 = strtotime('21:00:00');
            if($now_time > $time_7) {
                $sleep = $time_21 - $now_time;
            }else{
                $sleep = $time_7 - $now_time;
            }
            sleep($sleep);
        }
    }
}