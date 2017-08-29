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
    public function run($appId)
    {
        echo "执行时间 :". date('Y-m-d H:i:s') ."\n";
        $auth = AuthorizerUtil::getAuthByOne($appId);
        if(empty($auth) ||
            !isset($auth)) {
            echo "找不到对应公众号信息\n";exit;
        }
        $accessToken = $auth->authorizer_access_token;
        $rst = WeChatUserUtil::getUserListForOpenId($auth->authorizer_access_token);
        if(isset($rst['errcode']) || isset($rst['errmsg'])) {
            echo "Code :" .$rst['errcode'] . ' msg :' .$rst['errmsg'] ."\n";exit;
        }
        $openList = $rst['data']['openid'];
        $i = 0;
        foreach($openList as $openid ) {
            $client = AuthorizerUtil::getUserForOpenId($openid,$auth->record_id);
            if(!$client) {
                $getData = WeChatUserUtil::getUserInfo($accessToken, $openid);
                if(!isset($getData) || empty($getData)) {
                    echo '获取用户数据为空: openId: '.$openid .' accessToken:'.$accessToken."\n";
                    continue;
                }
                if($getData['errcode'] != 0 || !$getData) {
                    echo '获取用户数据为空2: openId: '.$openid .' accessToken:'.$accessToken ."\n";
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
        $time = date('Y-m-d H:i:s');
        echo "粉丝数 ".$rst['total'] . "条;  更新成功 $i 条  date :$time \n";
    }

}