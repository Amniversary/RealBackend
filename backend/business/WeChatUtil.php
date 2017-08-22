<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/27
 * Time: 下午3:57
 */

namespace backend\business;


use common\components\UsualFunForNetWorkHelper;
use common\components\wxpay\lib\WxPayConfig;
use common\models\Authorization;
use common\models\AuthorizationList;
use yii\db\Query;
use yii\web\HttpException;

class WeChatUtil
{
    private $appId;
    private $appsecret;
    public $AppInfo;


    public function __construct()
    {
        $this->appId = WxPayConfig::APPID;
        $this->appsecret = WxPayConfig::APPSECRET;
        $this->AppInfo = Authorization::findOne(['app_id'=>WxPayConfig::APPID]);
    }

    /**
     * 获取微信授权 component_access_token
     * @return bool
     */
    public function getToken(&$error)
    {
        $data = [
            'component_appid'=>$this->appId,
            'component_appsecret'=>$this->appsecret,
            'component_verify_ticket'=>$this->AppInfo->verify_ticket
        ];
        $json = json_encode($data);
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$json),true);
        if(empty($rst)){
            $error = '没有获取到对应Token';
            return false;
        }
        $this->AppInfo->access_token = $rst['component_access_token'];
        if(!$this->AppInfo->save()){
            $error = '保存微信Token失败';
            \Yii::error($error. ' ：'. var_export($this->AppInfo->getErrors(),true));

            return false;
        }
        return true;
    }



    /**
     * 获取微信预授权码 pre_auth_code
     * @return bool
     */
    public function getAuthCode(&$rst,&$error)
    {
        $data = [
            'component_appid'=>$this->appId
        ];
        $json = json_encode($data);
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=%s',
            $this->AppInfo->access_token);
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$json),true);
        if(empty($rst)){
            $error = '获取预授权码auth_code失败';
            return false;
        }

        $this->AppInfo->pre_auth_code = $rst['pre_auth_code'];
        if(!$this->AppInfo->save()){
            $error = '保存微信预授权码失败';
            \Yii::error($error. ' ：'. var_export($this->AppInfo->getErrors(),true));
            return false;
        }
        return true;
    }

    /**
     * 使用授权码换取公众号的接口调用凭据和授权信息
     * @param $query_auth
     * @param $error
     * @return bool
     */
    public function getQueryAuth($query_auth,&$rst,&$error)
    {
        $data = [
            'component_appid'=>$this->appId,
            'authorization_code'=>$query_auth,
        ];
        $json = json_encode($data);
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=%s',
            $this->AppInfo->access_token);
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$json),true);
        if(empty($rst)){
            $error = '获取接口调用凭据和授权信息失败';
            return false;
        }
        return true;
    }


    /**
     * 获取授权方的帐号基本信息
     * @param $authorize_appid
     * @param $outInfo
     * @param $error
     * @return bool
     */
    public function getAuthorizeInfo($authorize_appid,&$outInfo,&$error)
    {
        $data = [
            'component_appid'=>$this->appId,
            'authorizer_appid'=>$authorize_appid,
        ];
        $json = json_encode($data);
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=%s',
            $this->AppInfo->access_token);
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$json),true);
        if(empty($rst)){
            $error = '获取授权公众号基础信息失败';
            return false;
        }
        $outInfo = $rst;
        return true;
    }


    /**
     * 用授权refresh_Token刷新凭证令牌
     * @return bool
     */
    public function refreshAuthToken()
    {
        $query = (new Query())
            ->select(['authorizer_appid','authorizer_refresh_token','nick_name','record_id'])
            ->from('wc_authorization_list')
            ->all();
        if(empty($query)) exit('没有数据');
        $date = date('Y-m-d H:i:s');
        $max = count($query);
        $timeout = 0;
        for($i = 0; $i < $max ; $i ++ ) {
            $data = [
                'component_appid'=>$this->appId,
                'authorizer_appid'=>$query[$i]['authorizer_appid'],
                'authorizer_refresh_token'=>$query[$i]['authorizer_refresh_token']
            ];
            $url = "https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=".$this->AppInfo->access_token;
            $json = json_encode($data);
            $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$json),true);
            if(!isset($rst['authorizer_access_token']) || !isset($rst['authorizer_refresh_token'])){
                if($timeout < 3){
                    $i --;
                    $timeout ++;
                    continue;
                }
                echo '刷新授权方令牌失败: AppId : '.$query[$i]['authorizer_appid']."  time : $date\n";
                \Yii::error("刷新授权方令牌失败: AppId: ".$query[$i]['authorizer_appid'] . 'data :'.var_export($rst,true));
                \Yii::getLogger()->flush(true);
                continue;
            }
            $AuthAppid = AuthorizationList::findOne(['authorizer_appid'=>$query[$i]['authorizer_appid']]);
            $AuthAppid->authorizer_access_token = $rst['authorizer_access_token'];
            $AuthAppid->authorizer_refresh_token = $rst['authorizer_refresh_token'];
            $AuthAppid->update_time = $date;
            if(!$AuthAppid->save()){
                if($timeout < 3){
                    $i --;
                    $timeout ++;
                    continue;
                }
                \Yii::error('保存授权数据失败: Nick_name :'.$query[$i]['nick_name'] .'  '.var_export($AuthAppid->getErrors(),true));
                \Yii::getLogger()->flush(true);
                echo '保存授权新数据失败: '."time: $date\n";
                continue;
            }
            $timeout = 0;
        }
        echo "刷新授权公众号AppId完成，共 $max 条记录 , 成功刷新记录数:". $i ." 条,  time:  $date \n";
    }


    /**
     * 保存授权信息
     * @param $AuthInfo
     * @param $authorizer_info
     * @param $error
     * @return bool
     */
    public function SaveAuthInfo($AuthInfo,$authorizer_info,&$error)
    {
        $model = AuthorizationList::findOne(['authorizer_appid'=>$AuthInfo['authorizer_appid']]);
        if($model){
            $model->authorizer_access_token = $AuthInfo['authorizer_access_token'];
            $model->authorizer_refresh_token = $AuthInfo['authorizer_refresh_token'];
            $model->func_info = $AuthInfo['func_info'];
            $model->nick_name = $authorizer_info['nick_name'];
            $model->head_img = $authorizer_info['head_img'];
            $model->service_type_info = $authorizer_info['service_type_info']['id'];
            $model->verify_type_info = $authorizer_info['verify_type_info']['id'];
            $model->alias = $authorizer_info['alias'];
            $model->qrcode_url = $authorizer_info['qrcode_url'];
            $model->business_info = json_encode($authorizer_info['business_info']);
            $model->signature = $authorizer_info['signature'];
            $model->authorization_info = json_encode($AuthInfo);
            $model->update_time = date('Y-m-d H:i:s');
        }else{
            $model = new AuthorizationList();
            $model->authorizer_appid = $AuthInfo['authorizer_appid'];
            $model->authorizer_access_token = $AuthInfo['authorizer_access_token'];
            $model->authorizer_refresh_token = $AuthInfo['authorizer_refresh_token'];
            $model->func_info = json_encode($AuthInfo['func_info']);
            $model->status = 1;
            $model->user_id = \Yii::$app->user->id;
            $model->nick_name = $authorizer_info['nick_name'];
            $model->head_img = $authorizer_info['head_img'];
            $model->service_type_info = $authorizer_info['service_type_info']['id'];
            $model->verify_type_info = $authorizer_info['verify_type_info']['id'];
            $model->user_name = $authorizer_info['user_name'];
            $model->alias = $authorizer_info['alias'];
            $model->qrcode_url = $authorizer_info['qrcode_url'];
            $model->business_info = json_encode($authorizer_info['business_info']);
            $model->idc = $authorizer_info['idc'];
            $model->principal_name = $authorizer_info['principal_name'];
            $model->signature = $authorizer_info['signature'];
            $model->authorization_info = json_encode($AuthInfo);
            $model->create_time = date('Y-m-d H:i:s');
            $model->update_time = '';
        }

        if(!$model->save()) {
            $error = '保存授权公众号信息失败';
            \Yii::error($error .' ：' .var_export($model->getErrors(),true));
            return false;
        }
        return true;
    }

    /**
     * 上传微信临时素材
     * @param $file  //上传素材的物理路径(本地)
     * @param $access_token //公众号token
     * @param $rst  //返回的结果
     * @param $error  //错误信息
     * @param string $type //上传文件类型
     * @return bool
     */
    public function Upload($file,$access_token,&$rst,&$error,$type = 'image')
    {
        $data['media'] = class_exists('\CURLFile') ? new \CURLFile(realpath($file)): '@'.realpath($file);
        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=$access_token&type=$type";
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$data),true);
        if(!isset($rst['media_id'])){
            $error = '上传微信文件失败，没有获取到 media_id.   Code:'.$rst['errcode']. ' msg:'.$rst['errmsg'] .'pic:'.$file;
            \Yii::error($error.' -' . var_export($rst,true));
            return false;
        }

        return true;
    }

    /**
     * 上传微信素材
     * @param $picUrl   //上传图片Url
     * @param $access_token //微信token
     * @return bool|int|array  [
     *                              'type'=>image
     *                              'media_id'=>media_id
     *                              'created_at'=>1500032112
     *                          ]
     * @throws HttpException
     */
    public function UploadWeChatImg($picUrl,$access_token)
    {
        $file = basename($picUrl);
        $rst = UsualFunForNetWorkHelper::HttpGetImg($picUrl,$content_type,$error);
        if(empty($rst) || $rst == false){
            throw new HttpException(500,'获取不到对应网络图片');
        }
        $filename = $file;
        $dirname = \Yii::$app->getBasePath().'/web/wximages/';
        if(!file_exists($dirname)){
            mkdir($dirname,0777,true);
        }
        $fileDir = $dirname.$filename;
        file_put_contents($fileDir,$rst);
        if(!file_exists($fileDir)){
            throw new HttpException(500,'保存七牛图片到本地失败.');
        }
        if(!$this->Upload($fileDir,$access_token,$rst,$error)){
            throw new HttpException(500,$error);
        }
        @unlink($fileDir);
        return $rst;
    }


    /**
     * 上传微信语音素材
     * @param $videoUrl
     * @param $access_token
     * @return mixed
     * @throws HttpException
     */
    public function UploadVideo($videoUrl,$access_token)
    {
        $basename = basename($videoUrl);
        $rst = UsualFunForNetWorkHelper::HttpGet($videoUrl);
        if(empty($rst) || $rst == false) {
            throw new HttpException(500,'获取不到网络音频文件');
        }
        $dirname = \Yii::$app->basePath.'/web/wximages/';
        if(!file_exists($dirname)) {
            mkdir($dirname,0777);
        }
        $fileDir = $dirname.$basename;
        file_put_contents($fileDir,$rst);
        if(!file_exists($fileDir)) {
            throw new HttpException(500,'保存音频文件到本地失败.');
        }
        if(!$this->Upload($fileDir, $access_token, $rst, $error,'voice')) {
            throw new HttpException(500,$error);
        }
        @unlink($fileDir);
        return $rst;
    }
}