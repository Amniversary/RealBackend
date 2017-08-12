<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/21
 * Time: 下午5:17
 */

namespace frontend\controllers\WeiXinActions;


use frontend\business\ClientUtil;
use yii\base\Action;

class GetUserInfoAction extends Action
{
    public function run()
    {
        $rst = ['code'=>1,'msg'=>''];
        $post = json_decode(file_get_contents("php://input"),true);
        $open_id = $post['openid'];
        $app_id = $post['appid'];
        if(empty($post)) {
            $rst['msg'] = '请求参数不能为空';
            echo json_encode($rst);exit;
        }
        if(!isset($open_id)) {
            $rst['msg'] = 'OpenId Not Isset';
            echo  json_encode($rst);exit;
        }
        if(!isset($app_id)) {
            $rst['msg'] = 'AppId Not Isset';
            echo  json_encode($rst);exit;
        }
        $auth = ClientUtil::getAuthOne($app_id);
        $userData = ClientUtil::getUserForOpenId($open_id,$auth->record_id);
        if(empty($userData)) {
            $rst['msg'] = '找不到用户信息';
            \Yii::error('UserInfo: openId:'.$open_id . ' auth:'. $auth->authorizer_appid);
            echo json_encode($rst);exit;
        }

        $rst['code'] = 0;
        $rst['data'] = [
            'id'=>intval($userData->client_id),
            'nick_name'=>$userData->nick_name,
            'pic'=>$userData->headimgurl,
            'is_vip'=>intval($userData->is_vip),
            'create_time'=>strtotime($userData->create_time)
        ];

        echo json_encode($rst);
        exit;
    }

}