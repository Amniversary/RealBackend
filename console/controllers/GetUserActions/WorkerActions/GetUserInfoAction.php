<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/11
 * Time: 下午2:30
 */

namespace console\controllers\GetUserActions\WorkerActions;


use backend\business\AuthorizerUtil;
use backend\business\WeChatUserUtil;
use console\controllers\BackendActions\GetOpenIdForUserInfoAction;
use udokmeci\yii2beanstalk\BeanstalkController;
use yii\base\Action;
use yii\helpers\Console;

class GetUserInfoAction extends Action
{
    public function run($job)
    {
        $jobId = $job->getId();
        $sentData = $job->getData();
        fwrite(STDOUT, Console::ansiFormat(date('Y-m-d H:i:s') . "--$sentData->key_word-- in job id:[$jobId]--\n", [Console::BG_BLUE]));
        try {
            if (empty($next_openid)) $next_openid = '';
            if (empty($sentData->app_id) || !isset($sentData->app_id)) {
                fwrite(STDOUT, Console::ansiFormat("appId 为空:" . $sentData->app_id . "\n", [Console::BG_RED]));
                return BeanstalkController::DELETE;
            }
            fwrite(STDOUT, Console::ansiFormat("执行时间 :" . date('Y-m-d H:i:s') . "\n", [Console::BG_BLUE]));
            $this->getUserListForClient($sentData->app_id, $next_openid, $total, $i);
            $time = date('Y-m-d H:i:s');
            fwrite(STDOUT, Console::ansiFormat("粉丝数 " . intval($total) . "条;  更新成功 ". intval($i) ."条  date :$time \n", [Console::BG_YELLOW]));
            return BeanstalkController::DELETE;
        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e->getMessage() . "\n", [Console::BG_RED]));
            return BeanstalkController::DELETE;
        }
    }

    private function getUserListForClient($appId, $next_openid = null, &$total, &$i)
    {
        $auth = AuthorizerUtil::getAuthByOne($appId);
        $accessToken = $auth->authorizer_access_token;
        $rst = WeChatUserUtil::getUserListForOpenId($accessToken, $next_openid);
        if (isset($rst['errcode'])) {
            var_dump($rst);
            return false;
        }
        $total = $rst['total'];
        if (!isset($rst['data']['openid'])) {
            return false;
        }
        $openList = $rst['data']['openid'];
        $i = 0;
        foreach ($openList as $openid) {
            $client = AuthorizerUtil::getUserForOpenId($openid, $auth->record_id);
            if (!$client) {
                $getData = WeChatUserUtil::getUserInfo($accessToken, $openid);
                 if (!$getData) {
                    fwrite(STDOUT, Console::ansiFormat("获取用户数据为空: openId:  " . $openid ." accessToken: " . $accessToken . "\n", [Console::BG_BLUE]));
                    continue;
                }
                $getData['app_id'] = $auth->record_id;
                $model = AuthorizerUtil::genModel($client, $getData);
                if (!$model->save()) {
                    $error = '保存已关注微信用户信息失败 ';
                    \Yii::error($error. ' :'. var_export($model->getErrors(),true));
                    \Yii::getLogger()->flush(true);
                    fwrite(STDOUT, Console::ansiFormat($error. "\n", [Console::BG_BLUE]));
                    continue;
                }
                fwrite(STDOUT, Console::ansiFormat("新增用户 :" . $model->nick_name . " open_id :" . $model->open_id . "\n", [Console::BG_BLUE]));
                $i++;
            }
        }
        if (!empty($rst['next_openid'])) {
            $this->getUserListForClient($appId, $rst['next_openid'], $total, $num);
            $i += $num;
        }
        return true;
    }
}