<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/31
 * Time: 下午3:57
 */

namespace console\controllers;


use backend\business\AuthorizerUtil;
use backend\business\JobUtil;
use backend\components\TemplateComponent;
use yii\console\Controller;
use yii\db\Query;

class TemplateController extends Controller
{
    public function actionSendtemplatemsg($app_id)
    {
        set_time_limit(0);
        $auth = AuthorizerUtil::getAuthByOne($app_id);
        if(empty($auth)) {
            echo "找不到对应公众号 \n"; exit;
        }
        $template_id = 'DIWFZf7uyxBWl8cC5PldnN4QjY_Iihw6x5Ho6OlIi5Y';
        $accessToken = $auth->authorizer_access_token;
        $url = 'http://novel.duobb.cn/novel/vipremind?app=4';
        $query = (new Query())
            ->from('wc_client')
            ->select(['client_id','open_id','nick_name','app_id'])
            ->where(['app_id'=>$app_id,'is_vip'=>0,'subscribe'=>1])
            ->all();
        $template = new TemplateComponent($app_id, $accessToken);
        $i = 0;
        foreach($query as $item) {
            $data = [
                'first'=>[
                    'value'=>'您好，您的『免费开通会员』特权即将到期。',
                    'color'=>'#173177'
                ],
                'keyword1'=>[
                    'value'=>$item['nick_name'],
                    'color'=>'#173177',
                ],
                'keyword2'=>[
                    'value'=>'未知',
                    'color'=>'#173177',
                ],
                'keyword3'=>[
                    'value'=>'邀您免费开通会员',
                    'color'=>'#173177',
                ],
                'keyword4'=>[
                    'value'=>date('Y-m-d', strtotime('+3 day')),
                    'color'=>'#173177',
                ],
                'remark'=>[
                    'value'=>"到期之前免费开通会员，您将永久免费阅读我的书库所有书籍，更新量超过一万册。\n免费查看 >>> 开通方法",
                    'color'=>'#173177',
                ]
            ];
            $msg = $template->BuildTemplate($item['open_id'], $template_id,$data,$url);
            $params = [
                'key_word' => 'send_template',
                'nick_name' => $item['nick_name'],
                'open_id'=> $item['open_id'],
                'accessToken' => $accessToken,
                'msg' => $msg
            ];
            if(!JobUtil::AddCustomJob('templateBeanstalk', 'send_template', $params, $error)) {
                var_dump($error);
                continue;
            }
            
            $i ++;
        }
        echo "发送消息数 : $i 条";
    }
}