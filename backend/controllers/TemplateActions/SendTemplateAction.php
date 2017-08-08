<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/3
 * Time: 下午2:43
 */

namespace backend\controllers\TemplateActions;


use backend\business\AuthorizerUtil;
use backend\business\JobUtil;
use backend\business\TemplateUtil;
use backend\business\WeChatUserUtil;
use backend\components\TemplateComponent;
use yii\base\Action;

class SendTemplateAction extends Action
{
    public function run($t)
    {
        $rst = ['code'=>1, 'msg'=>''];
        $auth = WeChatUserUtil::getCacheInfo();
        $id = \Yii::$app->request->get('id');
        $templateData = TemplateUtil::GetTemplateById($id);
        $post = \Yii::$app->request->post('Template');
        if($t == 'test') {
            if(!isset($post['openid'])) {
                $rst['msg'] = '参数openId不能为空';
                echo json_encode($rst);exit;
            }
            if(strlen($post['openid']) != 28) {
                $rst['msg'] = '参数OpenId格式错误';
                echo json_encode($rst);exit;
            }
            $client = AuthorizerUtil::getUserForOpenId($post['openid'],$auth['record_id']);
            if(!isset($client) || empty($client)) {
                $rst['msg'] = '找不到对应用户数据信息';
                echo json_encode($rst);exit;
            }
            $open_id = $post['openid'];
            $url = $post['url'];
            unset($post['url']);
            unset($post['openid']);
            $accessToken = $auth['authorizer_access_token'];
            $template = new TemplateComponent(null,$accessToken);
            $data = [];
            foreach($post as $key=>$v) {
                $value = str_replace('{{NICKNAME}}', $client->nick_name, $v['value']);
                $data[$key] = ['value'=>$value, 'color'=> $v['color']];
            }
            $sendData = $template->BuildTemplate($open_id,$templateData->template_id,$data,$url);
            \Yii::error('template:'.var_export($sendData,true));
            $res = $template->SendTemplateMessage($sendData);
            if($res['errcode'] != 0) {
                $rst['msg'] = 'Code :'. $res['errcode'] .'  msg :'.$res['errmsg'];
                echo json_encode($rst);exit;
            }
        } else {
            unset($post['openid']);
            $params = [
                'key_word' => 'send_template',
                'id'=>$id,
                'data'=>$post,
                'app_id'=>$auth['record_id']
            ];
            if(!JobUtil::AddCustomJob('templateBeanstalk','send_template',$params,$error)) {
                $rst['msg'] = $error;
                echo json_encode($rst);exit;
            }
        }
        sleep(1);
        $rst['code'] = 0;
        echo json_encode($rst);
    }
}