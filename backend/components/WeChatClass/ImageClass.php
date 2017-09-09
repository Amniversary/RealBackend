<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/1
 * Time: 下午2:43
 */

namespace backend\components\WeChatClass;


use backend\business\AuthorizerUtil;
use backend\business\JobUtil;
use backend\business\WeChatUserUtil;
use backend\components\MessageComponent;

class ImageClass
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function image()
    {
        $appId = $this->data['appid'];
        $openid = $this->data['FromUserName'];
        $auth = AuthorizerUtil::getAuthOne($appId);
        $accessToken = $auth->authorizer_access_token;
        $User = AuthorizerUtil::getUserForOpenId($openid, $auth->record_id);
        if(empty($User) || !isset($User)) {
            $getData = WeChatUserUtil::getUserInfo($accessToken, $openid);
            if(!$getData) return null;
            $getData['app_id'] = $auth->record_id;
            $model = AuthorizerUtil::genModel($User,$getData);
            if(!$model->save()){
                $error ='保存已关注微信用户信息失败';
                \Yii::error($error. ' :'.var_export($model->getErrors(),true));
                return null;
            }
        }
        if(in_array($auth->record_id,\Yii::$app->params['WxVipParams'])) {
            $content = $this->getImageGenVip();
            return $content;
        }
        $msgObj = new MessageComponent($this->data, 1);
        //TODO: type 类型 3 图片匹配  4 语音匹配 5 视频匹配
        $content = $msgObj->getKeyImageMessage(3);
        return $content;
    }

    /**
     * 接收图片消息  更新用户vip状态
     * @return null|string
     */
    public function getImageGenVip()
    {
        $appId = $this->data['appid'];
        $openid = $this->data['FromUserName'];
        $auth = AuthorizerUtil::getAuthOne($appId);
        $accessToken = $auth->authorizer_access_token;
        $User = AuthorizerUtil::getUserForOpenId($openid, $auth->record_id);
        if($User->is_vip == 1) {
            return null;
        }
        $User->is_vip = 1;
        $User->remark1 =  date('Y-m-d H:i:s');
        if(!$User->save()) {
            \Yii::error('更新用户Vip状态失败: '. ' '.  var_export($User->getErrors(),true));
            return null;
        }
        switch($auth->record_id) {
            case 84: $num = 2; break;
            case 85: $num = 1; break;
            case 86: $num = 3; break;
            case 89: $num = 4; break;
            default: $num = 0; break;
        }
        $text = sprintf("恭喜您，您已成功开通会员，点击底部菜单【免费书库】即可阅读！\n点击☞☞<a href='http://novel.duobb.cn/novel/bookstore?app=%s'>【免费阅读】</a>",$num);
        $item = ['msg_type'=>0, 'content'=>$text];
        $params = [
            'key_word'=>'send_vip_msg',
            'open_id'=>$openid,
            'app_id' => $auth->record_id,
            'accessToken'=>$accessToken,
            'item' => $item
        ];
        if(!JobUtil::AddCustomJob('vipBeanstalk','send_vip_msg', $params, $error)) {
            \Yii::error($error);
            return null;
        }
        $content = '图片正在人工审核中';
        return $content;
    }
}