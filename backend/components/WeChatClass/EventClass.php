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
use backend\components\ReceiveType;

class EventClass
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    /* Array
(
  [subscribe] => 1
[openid] => oB4Z-wf0FYMlI7fW4ZvD90Y06RxA
[nickname] => Gavean
[sex] => 1
[language] => zh_CN
[city] => 杭州
[province] => 浙江
[country] => 中国
[headimgurl] => http://wx.qlogo.cn/mmopen/UVzXBswyibFh7ib0qClxDP6Y5EFUGSgrw7FIUNcB7K60LAIpKHpqHxJa7ta10HKYYIVSCPSQy0IBzGib9zgn9NE00vaHbVydjpY/0
[subscribe_time] => 1498896058
[unionid] => oVKOWs5xZRtfQNm73g5A4Fk7HO0M
[remark] =>
[groupid] => 0
[tagid_list] => Array
(
)

)*/
    /**
     * 处理微信关注事件
     */
    public function subscribe()
    {
        $appid = $this->data['appid'];
        $openid = $this->data['FromUserName'];
        //TODO: 如果已关注 查出用户信息
        $UserInfo = AuthorizerUtil::getAuthOneForOpenId($openid,$appid);
        $openInfo = AuthorizerUtil::getAuthOne($appid);
        if(empty($UserInfo) && !isset($UserInfo)){
            //TODO: 未关注重新请求用户信息
            $UserInfo = WeChatUserUtil::getUserInfo($openInfo->authorizer_access_token,$openid);
            $UserInfo['open_id'] = $UserInfo['openid'];
            unset($UserInfo['openid']);
            $flag = false;
        }else{
            $flag = true;
        }
        $model = AuthorizerUtil::genModel($UserInfo,$appid,$flag);
        if(!$model->save()){
            \Yii::error('保存微信用户信息失败：'.var_export($model->getErrors(),true));
            $content = null;
        }
        //TODO: 处理回复消息逻辑 走客服消息接口 回复多条消息
        $msgData = AuthorizerUtil::getAttentionMsg($openInfo->record_id);
        if(!empty($msgData)){
            foreach ($msgData as $item){
                if(!isset($item['msg_type'])){
                    $item['msg_type'] = 1;
                }
                WeChatUserUtil::sendCustomerMsg($openInfo->authorizer_access_token,$openid,$item);
            }
        }

        //$resultXml = ReceiveType::transmitText($this->data,$content);
        return null;
    }

    /**
     * 处理用户取消关注
     */
    public function unSubscribe()
    {
        $appid = $this->data['appid'];
        $openid = $this->data['FromUserName'];
        //TODO: 如果已关注 查出用户信息
        $UserInfo = AuthorizerUtil::getAuthOneForOpenId($openid,$appid);
        if(!empty($UserInfo)){
            $UserInfo->subscribe = 0;
            $UserInfo->save();
        }
        return null;
    }
}