<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/14
 * Time: 下午4:21
 */

namespace frontend\components;


use common\components\UsualFunForNetWorkHelper;

class DuobbComponent
{
    const AppID = 'wxeee51c79007f9b22'; //TODO: 微信小程序AppID
    const AppSecret = '1352bcad139100c2b38d9722a81ffba4'; //TODO: 微信小程序 AppSecret

    public $sessionKey;  //TODO: 微信小程序 sessionKey


    /**
     * 使用Code 换取用户openid 和 sessionKey
     * @param $code
     * @return bool|mixed
     */
    public function getCodeBySessionKey($code)
    {
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . self::AppID .
            "&secret=" . self::AppSecret . "&js_code=$code&grant_type=authorization_code";
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url),true);
        if(isset($rst['errcode'])) {
            \Yii::error('获取sessionKey :'. var_export($rst,true));
        }
        return isset($rst['errcode'])? false : $rst;
    }

}