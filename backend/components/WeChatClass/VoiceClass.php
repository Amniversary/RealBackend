<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/1
 * Time: 下午2:43
 */

namespace backend\components\WeChatClass;


use backend\business\AuthorizerUtil;
use backend\business\WeChatUserUtil;
use backend\components\MessageComponent;

class VoiceClass
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function Voice()
    {
        $openid = $this->data['FromUserName'];
        $appId = $this->data['appid'];
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
        $msgObj = new MessageComponent($this->data, 1);
        //TODO: 匹配类型 type  3 图片匹配  4 语音匹配 5 视频匹配
        $content = $msgObj->getKeyImageMessage(4);
        return $content;
    }
}