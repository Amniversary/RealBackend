<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/9
 * Time: 下午5:14
 */

namespace backend\controllers\TemplateActions;


use backend\business\AuthorizerUtil;
use backend\business\JobUtil;
use backend\business\UserUtil;
use backend\business\WeChatUserUtil;
use backend\business\WeChatUtil;
use backend\components\MessageComponent;
use yii\base\Action;

class SendCustomerAction extends Action
{
    public function run($id)
    {
        $rst = ['code'=>1,  'msg'=>''];
        if(empty($id) || !isset($id)) {
            $rst['msg'] = 'id 不能为空';
            echo json_encode($rst);exit;
        }
        $cache = WeChatUserUtil::getCacheInfo();
        $auth = AuthorizerUtil::getAuthByOne($cache['record_id']);
        $accessToken = $auth->authorizer_access_token;
        $post = \Yii::$app->request->post('AttentionEvent');
        $User = UserUtil::GetClientInfo($id);
        $uptime = strtotime($User->update_time);
        $now = time();
        $time = ($now - $uptime) / 86400;
        if($time >= 2) {
            $rst['msg'] = '该粉丝已超过48小时没有互动，无法发送回复消息 !';
            echo json_encode($rst);exit;
        }
        $data = [];
        switch ($post['msg_type']){
            case 0:
                $data = [
                    'content'=>$post['content'],
                    'msg_type'=>$post['msg_type']
                ];break;
            case 1:
                $arr = [
                    'title'=>$post['title'],
                    'description'=>$post['description'],
                    'url'=>$post['url'],
                    'picurl'=>$post['picurl']
                ];
                $data['msg_type'] = $post['msg_type'];
                $data[] = $arr;
                break;
            case 2:
                $rst = (new WeChatUtil())->UploadWeChatImg($post['picurl1'],$accessToken);
                $data = ['msg_type'=>$post['msg_type'],'media_id'=>$rst['media_id']];
                break;
            case 3:
                $video = (new WeChatUtil())->UploadVideo($post['video'], $accessToken);
                $data = ['msg_type'=>$post['msg_type'],'media_id'=>$video['media_id']];
                break;
        }
        $paramData = [
            'key_word'=>'wx_msg',
            'open_id'=>$User->open_id,
            'app_id' => $auth->record_id,
            'authorizer_access_token'=>$accessToken,
            'item'=>$data
        ];
        if(!JobUtil::AddCustomJob('wechatBeanstalk','wechat',$paramData,$error)){
            \Yii::error('keyword msg job is error :'.$error);
        }

        $rst['code'] = 0;
        echo json_encode($rst);
    }
}