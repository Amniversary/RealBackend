<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/27
 * Time: 下午3:57
 */

namespace backend\business;


use common\components\UsualFunForNetWorkHelper;
use common\models\Authorization;

class WeChatUtil
{
    private $appId;
    private $appsecret;


    public function __construct($app_id,$appsecret)
    {
        $this->appId = $app_id;
        $this->appsecret = $appsecret;
    }

    /**
     * 获取微信授权 component_access_token
     * @return bool
     */
    public function getToken(&$error)
    {
        $AppInfo = Authorization::findOne(['app_id'=>$this->appId]);
        $data = [
            'component_appid'=>$this->appId,
            'component_appsecret'=>$this->appsecret,
            'component_verify_ticket'=>$AppInfo->verify_ticket
        ];
        $json = json_encode($data);
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$json),true);
        if(empty($rst)){
            $error = '没有获取到对应Token';
            return false;
        }
        $AppInfo->access_token = $rst['component_access_token'];
        if(!$AppInfo->save()){
            $error = '保存微信Token失败';
            \Yii::error($error. ' ：'. var_export($AppInfo->getErrors(),true));
            return false;
        }
        return true;
    }

    /**
     * 获取微信预授权码 pre_auth_code
     * @return bool
     */
    public function getAuthCode(&$error)
    {
        $AppInfo = Authorization::findOne(['app_id'=>$this->appId]);
        $data = [
            'component_appid'=>$this->appId
        ];
        $json = json_encode($data);
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=%s',$AppInfo->access_token);
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$json),true);
        if(empty($rst)){
            $error = '获取预授权码auth_code失败';
            return false;
        }
        $AppInfo->pre_auth_code = $rst['pre_auth_code'];
        if(!$AppInfo->save()){
            $error = '保存微信预授权码失败';
            \Yii::error($error. ' ：'. var_export($AppInfo->getErrors(),true));
            return false;
        }
        return true;
    }

}