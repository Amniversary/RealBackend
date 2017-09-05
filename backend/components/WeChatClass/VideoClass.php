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

class VideoClass
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function Video()
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
            $model = AuthorizerUtil::genModel($User, $getData);
            if(!$model->save()){
                \Yii::error('保存用户信息失败 -- video'. var_export($model->getErrors(),true));
                return null;
            }
        }
        $msgObj = new MessageComponent($this->data, 1);
        //TODO: 匹配类型  3图片匹配  4语音匹配 5视频匹配
        $content = $msgObj->getKeyImageMessage(5);
        return $content;
    }
}