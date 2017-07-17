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
use backend\business\WeChatUtil;
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
                    break;
                }
            }
            $msgData = $this->getMessageModel();
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
            $this->sendMessageCustom($msgData);
            return null;
        } else {
            $msgData = $this->getMessageModel();
            if(!$msgData) return null;
            $rst = $msgData[0];
        }
        return $rst;
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
                    $rst = $this->DisposeImg($value['picurl'],$this->auth->authorizer_access_token,$value['update_time'],$value['record_id']);
                    $data[] = ['msg_type'=>$value['msg_type'],'media_id'=>$rst['media_id']];
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
        $condition = sprintf('app_id=%s and flag=%s', $this->auth->record_id,$this->flag);
        if($this->key !== null)
            $condition .= sprintf(' and key_id=%s', $this->key);
        $query = (new Query())->from('wc_attention_event')
            ->select(['record_id','app_id','event_id','content','msg_type','title','description','url','picurl','media_id','update_time'])
            ->where($condition)->orderBy('order_no asc')->all();
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
    public static function DisposeImg($picurl,$access_token,$exceed,$record)
    {
        $time = time();
        $outTime = intval(($time - $exceed)/84600);
        $model = AuthorizerUtil::getEventMsg($record);
        if($outTime >= 3){
            $rst = (new WeChatUtil())->UploadWeChatImg($picurl,$access_token);
            $model->media_id = $rst['media_id'];
            $model->update_time = $rst['created_at'];
            $model->save();
            return $rst;
        }
        $rst = [
            'media_id'=>$model->media_id,
        ];
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
                $this->sendMessageCustom($msgData);
            }
        }
    }

    /**
     * 发送自定义消息
     */
    public function sendMessageCustom($msgData){
        if(!empty($msgData)) {
            foreach ($msgData as $item)
            {
                $paramData = [
                    'key_word'=>'key_word',
                    'open_id'=>$this->data['FromUserName'],
                    'authorizer_access_token'=>$this->auth->authorizer_access_token,
                    'item'=>$item,
                ];
                if(!JobUtil::AddCustomJob('wechatBeanstalk','wechat',$paramData,$error)){
                    \Yii::error('keyword msg job is error :'.$error);
                }
            }
        }
        return true;
    }

}
