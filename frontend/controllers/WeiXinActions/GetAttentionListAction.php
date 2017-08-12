<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/21
 * Time: 下午6:23
 */

namespace frontend\controllers\WeiXinActions;


use frontend\business\ClientUtil;
use yii\base\Action;

class GetAttentionListAction extends Action
{
    public function run()
    {
        $rst = ['code'=>1 ,'msg'=>''];
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
        $user = ClientUtil::getUserForOpenId($open_id,$auth->record_id);
        $rul = ClientUtil::getAttensionList($user->client_id,$auth->record_id);

        $rst['code'] = 0;
        $rst['data'] = $rul;
        echo json_encode($rst);
        exit;
    }
}