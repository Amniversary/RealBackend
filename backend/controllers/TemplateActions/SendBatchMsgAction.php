<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/23
 * Time: 下午4:42
 */

namespace backend\controllers\TemplateActions;

use backend\business\AuthorizerUtil;
use backend\business\JobUtil;
use backend\business\WeChatUserUtil;
use backend\business\WeChatUtil;
use common\components\UsualFunForStringHelper;
use common\models\TemplateTiming;
use yii\base\Action;

class SendBatchMsgAction extends Action
{
    public function run($t)
    {
        $rst = ['code'=>1 ,'msg'=>''];
        $data = [];
        $cache = WeChatUserUtil::getCacheInfo();
        $auth =  AuthorizerUtil::getAuthByOne($cache['record_id']);
        $accessToken = $auth->authorizer_access_token;
        $post = \Yii::$app->request->post('AttentionEvent');
        if(empty($post) || !isset($post)) {
            $rst['msg'] = 'post 消息体不能为空';
            echo json_encode($rst);exit;
        }
        $time = $post['time'];
        unset($post['time']);
        $openId = $post['openid'];
        unset($post['openid']);
        if(!empty($post) && isset($post)) {
            $data = WeChatUserUtil::genMessageModel($post, $accessToken);
        }
        if($t == 'test') {
            if(!isset($openId)) {
                $rst['msg'] = '参数openId不能为空';
                echo json_encode($rst);exit;
            }
            if(strlen($openId) != 28) {
                $rst['msg'] = '参数OpenId格式错误';
                echo json_encode($rst);exit;
            }
            $User = AuthorizerUtil::getUserForOpenId($openId,$auth['record_id']);
            if(!isset($User) || empty($User)) {
                $rst['msg'] = '找不到对应用户数据信息';
                echo json_encode($rst);exit;
            }
            $data['openid'] = $openId;
        }
        if (!empty($time)) {
            if(!UsualFunForStringHelper::IsDateTime($time)) {
                $rst['msg'] = '时间格式不正确';
                echo json_encode($rst);exit;
            }
            $task = new TemplateTiming();
            $task->app_id = $auth->record_id;
            $task->template_data = json_encode($data,JSON_UNESCAPED_UNICODE);
            $task->status = 1;
            $task->type = ($t == 'test') ? 3 : 4;
            $task->create_time = strtotime($time);
            if(!$task->save()) {
                $rst['msg'] = '保存客服消息定时任务记录失败';
                \Yii::error($rst['msg']. ' :' . var_export($task,true));
                echo json_encode($rst);exit;
            }
            $rst['code'] = 0;
            echo json_encode($rst);exit;
        }
        $params = [
            'key_word' => 'send_batch_msg',
            'data'=>$data,
            'app_id'=>$auth->record_id,
            'type' => ($t == 'test') ? 3 : 4 ,
        ];
        if(!JobUtil::AddCustomJob('templateBeanstalk', 'send_user_msg', $params, $error, (60*60*5))) {
            \Yii::error($error);
            $rst['msg']  =$error;
            echo json_encode($rst);exit;
        }
        $rst['code'] = 0;
        echo json_encode($rst);
    }
}