<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/25
 * Time: 下午12:05
 */

namespace console\controllers\BackendActions;


use backend\business\AuthorizerUtil;
use backend\business\WeChatUserUtil;
use yii\base\Action;

class GetOpenIdForUserInfoAction extends Action
{
    public function run($appId, $next_openid)
    {
        if(empty($next_openid)) {
            $next_openid = '';
        }
        echo "执行时间 :". date('Y-m-d H:i:s') ."\n";
        $this->getUserListForClient($appId, $next_openid, $total, $i);
        $time = date('Y-m-d H:i:s');
        echo "粉丝数 ".$total . "条;  更新成功 $i 条  date :$time \n";

    }

    private function getUserListForClient($appId, $next_openid = null, &$total, &$i)
    {
        $auth = AuthorizerUtil::getAuthByOne($appId);
        $accessToken = $auth->authorizer_access_token;
        $rst = WeChatUserUtil::getUserListForOpenId($accessToken, $next_openid);
        if(isset($rst['errcode'])) {
            var_dump($rst);exit;
        }
        $total = $rst['total'];
        if(!isset($rst['data']['openid'])) {
            return false;
        }
        $openList = $rst['data']['openid'];
        $i = 0;
        foreach($openList as $openid ) {
            $client = AuthorizerUtil::getUserForOpenId($openid,$auth->record_id);
            if(!$client) {
                $getData = WeChatUserUtil::getUserInfo($accessToken, $openid);
                if(!$getData) {
                    echo '获取用户数据为空: openId: '.$openid .' accessToken:'.$accessToken."\n";
                    continue;
                }
                $getData['app_id'] = $auth->record_id;
                $model = AuthorizerUtil::genModel($client,$getData);
                if(!$model->save()){
                    echo '保存已关注微信用户信息失败'."\n";
                    print_r(var_export($model->getErrors(),true));
                    continue;
                }
                echo "新增用户 :".$model->nick_name. " open_id :".$model->open_id ."\n";
                $i ++ ;
            }
        }
        if(!empty($rst['next_openid'])) {
            $this->getUserListForClient($appId, $rst['next_openid'], $total, $num);
            $i += $num;
        }
        return true;
    }
}