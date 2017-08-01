<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/1
 * Time: 下午2:43
 */

namespace backend\components\WeChatClass;


use backend\business\AuthorizerUtil;
use backend\business\ImageUtil;
use backend\business\JobUtil;
use backend\business\SaveByTransUtil;
use backend\business\SaveRecordByTransactions\SaveByTransaction\SaveUserShareByTrans;
use backend\business\SaveRecordByTransactions\SaveByTransaction\StatisticFansUserByTrans;
use backend\business\WeChatUserUtil;
use backend\business\WeChatUtil;
use backend\components\MessageComponent;
use backend\components\ReceiveType;
use common\components\OssUtil;
use common\components\SystemParamsUtil;
use common\components\UsualFunForNetWorkHelper;
use common\models\QrcodeImg;
use common\models\QrcodeShare;
use EasyWeChat\MiniProgram\QRCode\QRCode;
use Qiniu\Auth;

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
        $openid = $this->data['FromUserName'];  //TODO: 触发事件用户openId
        $msgObj = new MessageComponent($this->data);

        $auth = AuthorizerUtil::getAuthOne($appid); //TODO: 获取公众号信息
        $access_token = $auth->authorizer_access_token;
        if(empty($auth)) return null;   //TODO: 如果公众号不存在
        if(isset($auth->record_id)) {   //TODO: 处理用户关注统计
            $DataPrams =['key_word'=>'attention','app_id'=>$auth->record_id, 'type'=>1];
            if(!JobUtil::AddCustomJob('attentionBeanstalk','attention',$DataPrams,$error))
                \Yii::error($error);
        }

        //TODO: 获取用户基本信息
        if(AuthorizerUtil::isVerify($auth->verify_type_info)) {
            $getData = WeChatUserUtil::getUserInfo($access_token,$openid); //TODO: 请求获取用户信息
            if(!isset($getData) || empty($getData)) {
                \Yii::error('获取用户信息：'.var_export($getData,true),' openId:'. $openid . ' accessToken:'.$access_token);
                return null;
            }
            if($getData['errcode'] != 0 || !$getData) {
                \Yii::error('获取用户信息:'. var_export($getData,true).' openId1:'. $openid. ' accessToken1:'. $access_token);
                return null;
            }
            $getData['app_id'] = $auth->record_id;
            $UserInfo = AuthorizerUtil::getUserForOpenId($openid,$auth->record_id);
            $model = AuthorizerUtil::genModel($UserInfo,$getData);
            if(!$model->save()){
                \Yii::error('保存微信用户信息失败：'.var_export($model->getErrors(),true));
                return null;
            }
            if(in_array($auth->record_id,[84,85,86,89])){
                if(empty($UserInfo)) {
                    $params = ['key_word' => 'get_qrcode', 'data' => $this->data];
                    if(!JobUtil::AddCustomJob('wechatBeanstalk','get_qrcode',$params,$error)) {
                        \Yii::error($error);
                    }
                }
            }
            $msgObj->sendQrcodeMessage($model);
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
        $DataPrams =['key_word'=>'cancel_attention','app_id'=>$AuthInfo->record_id, 'type'=>2];
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


    /**
     * 处理点击事件 生成二维码图片
     * @return null
     */
    public function  getQrCodeImg(&$error) {
        $openid = $this->data['FromUserName'];
        $auth = AuthorizerUtil::getAuthOne($this->data['appid']);
        $access_token = $auth->authorizer_access_token;
        $client = AuthorizerUtil::getUserForOpenId($openid,$auth->record_id);
        if(!$client) {
            $getData = WeChatUserUtil::getUserInfo($access_token, $openid);
            if(!isset($getData) || empty($getData)) {
                $error  = '获取用户数据为空: openId: '.$openid .' accessToken:'.$access_token;
                \Yii::error('获取用户信息2：'.var_export($getData,true),' openId:'. $openid . ' accessToken:'.$access_token);
                return false;
            }
            if($getData['errcode'] != 0 || !$getData) {
                $error  = '获取用户数据为空2: openId: '.$openid .' accessToken:'.$access_token;
                \Yii::error('获取用户信息3:'. var_export($getData,true).' openId1:'. $openid. ' accessToken1:'. $access_token);
                return false;
            }
            $getData['app_id'] = $auth->record_id;
            $model = AuthorizerUtil::genModel($client,$getData);
            if(!$model->save()){
                $error ='保存已关注微信用户信息失败';
                \Yii::error($error. ' :'.var_export($model->getErrors(),true));
                return false;
            }
            $client = $model;
        }
        $img = ImageUtil::GetQrcodeImg($client->client_id);
        if(!isset($img) || empty($img)) {  //TODO: 如果图片不存在  重新生成并上传
            $userData = WeChatUserUtil::getUserInfo($access_token,$openid);
            if(empty($userData['headimgurl'])) {
                $userData['headimgurl'] = 'http://7xld1x.com1.z0.glb.clouddn.com/timg.jpeg';
            }
            if(!WeChatUserUtil::getQrcodeSendImg($access_token,$openid,$userData['headimgurl'],$qrcode_file,$pic_file,$error)) {
                $error = '获取二维码图片失败 '.$error;
                return false;
            }
            $text = $userData['nickname'];
            if(!ImageUtil::imagemaking($qrcode_file,$pic_file,$openid,$text,$bg_img,$error)){
                return false;
            }
            if(!file_exists($bg_img)) {
                $error = '海报生成失败: bg_img:'. $bg_img . ' qrcode_img:' . $qrcode_file . ' pic:'. $pic_file;
                return false;
            }
            $wechat = new WeChatUtil();
            if(!$wechat->Upload($bg_img,$access_token,$rst,$error)) { //TODO: 背景图上传微信素材
                return false;
            }
            $model = new QrcodeImg();
            $model->client_id = $client->client_id;
            $model->media_id = $rst['media_id'];
            $model->update_time = $rst['created_at'];
            $model->save();
            $media_id = $model->media_id;
            $imgParams = ['key_word'=> 'delete_img','qrcode_file'=>$qrcode_file, 'pic_file'=>$pic_file];
            if(!JobUtil::AddCustomJob('wechatBeanstalk','delete_msg',$imgParams,$error)) {
                \Yii::error($error); \Yii::getLogger()->flush(true);
            }
        } else {
            $time = time();
            $outTime = intval(($time - $img->update_time) / 84600);
            if($outTime >= 3){
                $userData = WeChatUserUtil::getUserInfo($access_token,$openid);
                if(empty($userData['headimgurl'])) {
                    $userData['headimgurl'] = 'http://7xld1x.com1.z0.glb.clouddn.com/timg.jpeg';
                }
                if(!WeChatUserUtil::getQrcodeSendImg($access_token,$openid,$userData['headimgurl'],$qrcode_file,$pic_file,$error)) {
                    $error = '获取图片失败 '.$error;
                    return false;
                }
                $text = $userData['nickname'];
                if(!ImageUtil::imagemaking($qrcode_file,$pic_file,$openid,$text,$bg_img,$error)){
                    return false;
                }
                if(!file_exists($bg_img)) {
                    $error = '海报生成失败2: bg_img:'. $bg_img . ' qrcode_img:' . $qrcode_file . ' pic:'. $pic_file;
                    return false;
                }
                $wechat = new WeChatUtil();
                if(!$wechat->Upload($bg_img,$access_token,$rst,$error)) { //TODO: 背景图上传微信素材
                    return false;
                }
                $imgParams = ['key_word'=> 'delete_img','qrcode_file'=>$qrcode_file, 'pic_file'=>$pic_file];
                if(!JobUtil::AddCustomJob('wechatBeanstalk','delete_msg',$imgParams,$error)) {
                    \Yii::error($error); \Yii::getLogger()->flush(true);
                }
                $img->client_id = $client->client_id;
                $img->media_id = $rst['media_id'];
                $img->update_time = $rst['created_at'];
                $img->save();
            }
            $media_id = $img->media_id;
        }
        $msgObj = new MessageComponent($this->data);
        $msgData = [
            ['msg_type'=>'0', 'content'=>\Yii::$app->params['qrcode_msg'][0]],
            ['msg_type'=>'2', 'media_id'=>$media_id],
        ];
        $msgObj->sendMessageCustom($msgData,$openid);
        return true;
    }
}