<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/15
 * Time: 下午2:33
 */

namespace backend\components;


use backend\business\AuthorizerUtil;
use backend\business\JobUtil;
use backend\business\ResourceUtil;
use backend\business\SaveByTransUtil;
use backend\business\SaveRecordByTransactions\SaveByTransaction\SaveUserShareByTrans;
use backend\business\WeChatUtil;
use common\components\SystemParamsUtil;
use common\models\Resource;
use yii\db\Query;

class MessageComponent
{
    public $app_id;
    public $flag;
    public $key;
    public $data = null;
    public $auth = null;


    //TODO: flag 0关注消息  1关键词消息  2自定义菜单消息
    public function __construct($data,$flag = 0,$key = null ){
        $this->flag = $flag;
        $this->key = $key;
        $this->data = $data;
        $this->auth = AuthorizerUtil::getAuthOne($data['appid']);
    }

    /**
     * 关键字消息检测 公众号是否认证
     * @return null
     */
    public function VerifySendMessage(){
        if(AuthorizerUtil::isVerify($this->auth->verify_type_info)) {
            $this->VerifyKeyWord();
            return null;
        } else {
            $keyword = AuthorizerUtil::getKeyword($this->auth->record_id);
            foreach($keyword as $item){
                $touch = $item['rule'] == 1 ?
                    $this->data['Content'] == $item['keyword'] ? true:false :
                    strpos($item['keyword'],$this->data['Content']) !== false ? true:false;
                if($touch){
                    $this->key = $item['key_id'];
                    $msgData = $this->getMessageModel();
                    break;
                }
            }
            if(!$msgData) return null;
            $rst = $msgData[0];
        }
        return $rst;
    }

    /**
     * 关注消息检测 公众号是否认证
     * @return null
     */
    public function VerifySendAttentionMessage(){
        if(AuthorizerUtil::isVerify($this->auth->verify_type_info)) {
            $msgData = $this->getMessageModel();
            $this->sendMessageCustom($msgData,$this->data['FromUserName']);
            return null;
        } else {
            $msgData = $this->getMessageModel();
            if(!$msgData) return null;
            $rst = $msgData[0];
        }
        return $rst;
    }


    /**
     * 扫描二维码事件
     * @return null
     */
    public function sendQrcodeMessage($model){
        //TODO: 判断用户是否扫二维码关注
        if(isset($this->data['EventKey']) && !empty($this->data['EventKey'])) {
            $user_id = $this->data['EventKey'];
            if(strpos($user_id,'qrscene_') !== false) {
                $str = str_replace('qrscene_','',$this->data['EventKey']);
                $user_id = trim($str);
            }
            //TODO: 获取被扫者用户基本信息
            $userData = AuthorizerUtil::getUserForOpenId($user_id,$this->auth->record_id);
            $is_attention = AuthorizerUtil::isAttention($model->client_id); //TODO:  是否扫过其他人
            if(!$is_attention){
                \Yii::error('用户已关注: ClientId :'.$model->client_id . '  分享人ID:'.$userData->client_id);
            }
            if($is_attention)
            {
                if($user_id != $model->client_id)
                {
                    $transActions[] = new SaveUserShareByTrans($userData,$model);
                    if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error,$out))
                    {
                        \Yii::error($error);
                        return null;
                    }
                    $userData = AuthorizerUtil::getUserForOpenId($user_id,$this->auth->record_id);
                    $msg = SystemParamsUtil::GetSystemParam('qrcode_msg',true,'');
                    if(($userData->invitation <= 5)){
                        $Qcmsg[] = ['msg_type'=>0, 'content'=>sprintf($msg['value1'],($userData->invitation),$model->nick_name)];
                    }
                    if($userData->invitation == 5){
                        $Qcmsg[] = ['msg_type'=>0, 'content'=>$msg['value2']];
                    }
                    if(!empty($Qcmsg)) {
                        $this->sendMessageCustom($Qcmsg,$userData->open_id);
                    }
                }
            }
        }
        return null;
    }
    /**
     * 组装消息格式
     * @return array
     */
    public function getMessageModel(){
        $query = $this->getWxMessage();
        if(!$query) return false;
        $data = []; $temp = [];
        foreach ($query as $key => $value)
        {
            switch ($value['msg_type']){
                case 0:  //TODO: 文本消息
                    $data[] = [
                        'content'=>$value['content'],
                        'msg_type'=>$value['msg_type']
                    ];break;
                case 1: //TODO: 图文消息
                    $keys = array_search($value['event_id'],$temp);
                    $arr = [
                        'title'=>$value['title'],
                        'description'=>$value['description'],
                        'url'=>$value['url'],
                        'picurl'=>$value['picurl']
                    ];
                    if($keys === false) {
                        $temp[$key] = $value['event_id'];
                        $data[$key]['msg_type'] = $value['msg_type'];
                        $data[$key][] = $arr;
                    }else{
                        $data[$key]['msg_type'] = $value['msg_type'];
                        $data[$keys][] = $arr;
                    }break;
                case 2: //TODO: 图片消息
                    $rst = $this->DisposeImg($value['picurl'],$this->auth->authorizer_access_token,$this->auth->record_id,$value['record_id']);
                    $data[] = ['msg_type'=>$value['msg_type'],'media_id'=>$rst['media_id']];
                    break;
                case 3: //TODO: 语音消息
                    $video = $this->DisposeVideo($value['video'], $this->auth->authorizer_access_token, $this->auth->record_id, $value['record_id']);
                    $data[] = ['msg_type'=>$value['msg_type'],'media_id'=>$video['media_id']];
                    break;
            }
        }
        return $data;
    }

    /**
     * 获取消息列表
     * @return array|bool
     */
    public function getWxMessage(){
        $params = 'select msg_id from wc_batch_attention where app_id = %s';
        $condition = sprintf('app_id=%s and flag=%s or record_id in ('.$params.')', $this->auth->record_id,$this->flag, $this->auth->record_id);
        if($this->key !== null)
            $condition .= sprintf(' or key_id=%s', $this->key);
        $query = (new Query())->from('wc_attention_event')
            ->select(['record_id','app_id','event_id','content','msg_type','title','description','url',
                'picurl','media_id','update_time','video'])
            ->where($condition)
            ->orderBy('order_no asc,create_time asc')
            ->all();
        if(empty($query))
            return false;
        return $query;
    }

    /**
     * 处理过期图片
     * @param $picurl  //图片url
     * @param $access_token  //公众号access_token
     * @param $record   //消息记录id
     * @return array|bool|int
     * @throws \yii\web\HttpException
     */
    public static function DisposeImg($picurl,$access_token,$app_id,$record)
    {
        $time = time();
        $resource = ResourceUtil::GetResource($app_id,$record);
        if(!$resource) {
            $model = new Resource();
            $rst = (new WeChatUtil())->UploadWeChatImg($picurl,$access_token);
            $model->app_id = $app_id;
            $model->msg_id = $record;
            $model->media_id = $rst['media_id'];
            $model->update_time = $rst['created_at'];
            $model->save();
            return $rst;
        } else {
            $outTime = intval(($time - $resource->update_time)/84600);
            if($outTime >= 3){
                $rst = (new WeChatUtil())->UploadWeChatImg($picurl,$access_token);
                $resource->media_id = $rst['media_id'];
                $resource->update_time = $rst['created_at'];
                $resource->save();
            }
            $rst = [
                'media_id'=>$resource->media_id,
            ];
        }
        return $rst;
    }

    /**
     * 处理过期音频
     * @param $videoUrl
     * @param $access_toekn
     * @param $exceed
     * @param $record
     * @return array|mixed
     * @throws \yii\web\HttpException
     */
    public static function DisposeVideo($videoUrl,$access_token,$app_id,$record)
    {
        $time = time();
        $resource = ResourceUtil::GetResource($app_id,$record);
        if(!$resource) {
            $model = new Resource();
            $rst = (new WeChatUtil())->UploadVideo($videoUrl, $access_token);
            $model->app_id = $app_id;
            $model->msg_id = $record;
            $model->media_id = $rst['media_id'];
            $model->update_time = $rst['created_at'];
            $model->save();
            return $rst;
        } else {
            $outTime = intval(($time - $resource->update_time) / 84600);
            if($outTime >= 3) {
                $rst = (new WeChatUtil())->UploadVideo($videoUrl, $access_token);
                $resource->media_id = $rst['media_id'];
                $resource->update_time = $rst['created_at'];
                $resource->save();
            }
            $rst = [
                'media_id'=>$resource->media_id,
            ];
        }
        return $rst;
    }

    /**
     * 检测是否匹配关键字
     */
    public function VerifyKeyWord(){
        $keyword = AuthorizerUtil::getKeyword($this->auth->record_id);
        foreach($keyword as $item){
            $touch = $item['rule'] == 1 ?
                $this->data['Content'] == $item['keyword'] ? true:false :
                strpos($item['keyword'],$this->data['Content']) !== false ? true:false;
            if($touch){
                $this->key = $item['key_id'];
                $msgData = $this->getMessageModel();
                $this->sendMessageCustom($msgData,$this->data['FromUserName']);
            }
        }
    }

    /**
     * 发送自定义消息
     */
    public function sendMessageCustom($msgData,$openid)
    {
        if(!empty($msgData)) {
            foreach ($msgData as $item)
            {
                $paramData = [
                    'key_word'=>'key_word',
                    'open_id'=>$openid,
                    'authorizer_access_token'=>$this->auth->authorizer_access_token,
                    'item'=>$item
                ];
                if(!JobUtil::AddCustomJob('wechatBeanstalk','wechat',$paramData,$error)){
                    \Yii::error('keyword msg job is error :'.$error);
                }
            }
        }
        return true;
    }

    /**
     * 保存用户数据.
     * @return bool
     */
    public function SaveClient(){
        $UserInfo = AuthorizerUtil::getUserForOpenId($this->data['FromUserName'],$this->auth->record_id);
        if(empty($UserInfo)) {
            if(!AuthorizerUtil::SaveUserInfo($this->auth->authorizer_access_token,$this->data['FromUserName'],$this->auth->record_id,$UserInfo)) {
                return false;
            }
        }
        return true;
    }
}
