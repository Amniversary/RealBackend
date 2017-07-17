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
use backend\business\SaveByTransUtil;
use backend\business\SaveRecordByTransactions\SaveByTransaction\StatisticFansUserByTrans;
use backend\business\WeChatUserUtil;
use backend\components\MessageComponent;
use backend\components\ReceiveType;

class EventClass
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * 处理微信关注事件
     */
    public function subscribe()
    {
        $appid = $this->data['appid'];
        $openid = $this->data['FromUserName'];
        $msgObj = new MessageComponent($this->data);
        $flag = true;
        //TODO: 如果已关注 查出用户信息
        $openInfo = AuthorizerUtil::getAuthOne($appid);
        $UserInfo = AuthorizerUtil::getUserForOpenId($openid,$openInfo->record_id);
        //TODO: 处理用户关注统计
        $DataPrams =['key_word'=>'attention','app_id'=>$openInfo->record_id, 'type'=>1];
        if(!JobUtil::AddCustomJob('attentionBeanstalk','attention',$DataPrams,$error))
            \Yii::error($error);
        //TODO: 获取用户基本信息
        $getData = WeChatUserUtil::getUserInfo($openInfo->authorizer_access_token,$openid);
        if($getData['errcode'] != 0 ) {
            \Yii::error('获取用户信息：'.var_export($getData,true));
            return null;
        }
        $getData['open_id'] = $getData['openid']; unset($getData['openid']);
        if(empty($UserInfo) || !isset($UserInfo)) $flag = false;
        if(!isset($getData['open_id'])) return null;
        $getData['app_id'] = $openInfo['record_id'];
        $model = AuthorizerUtil::genModel($UserInfo,$getData,$flag);
        if(!$model->save()){
            \Yii::error('保存微信用户信息失败：'.var_export($model->getErrors(),true));
            return null;
        }
        //TODO: 处理回复消息逻辑 走客服消息接口 回复多条消息
        $msgData = $msgObj->VerifySendAttentionMessage();
        return $msgData;
    }

    /**
     * 处理用户取消关注
     */
    public function unSubscribe()
    {
        $AuthInfo = AuthorizerUtil::getAuthOne($this->data['appid']);
        if(empty($AuthInfo) || !isset($AuthInfo)){
            \Yii::error('找不到对应的公众号信息 ： AppId:'.$this->data['appid'] );
            return null;
        }
        $openid = $this->data['FromUserName'];
        //TODO: 如果已关注 查出用户信息
        $DataPrams =['key_word'=>'attention','app_id'=>$AuthInfo->record_id, 'type'=>2];
        if(!JobUtil::AddCustomJob('attentionBeanstalk','attention',$DataPrams,$error))
            \Yii::error($error);

        $UserInfo = AuthorizerUtil::getUserForOpenId($openid,$AuthInfo->record_id);
        if(!empty($UserInfo)){
            $UserInfo->subscribe = 0;
            $UserInfo->update_time = date('Y-m-d H:i:s');
            $UserInfo->save();
        }
        return null;
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
}