<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/1
 * Time: 下午3:08
 */

namespace backend\business;


use common\components\UsualFunForNetWorkHelper;

class WeChatUserUtil
{
    /**
     * 获取公众号用户基本信息
     * @param $access_token
     * @param $openid
     * @param string $lang
     * @return mixed
     */
    public static function getUserInfo($access_token,$openid,$lang = 'zh_CN')
    {
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=%s',
            $access_token,
            $openid,
            $lang);
        $rst = json_decode(UsualFunForNetWorkHelper::HttpGet($url),true);
        return $rst;
    }

    /**
     * 发送客服消息
     * @param $access_token
     * @param $openid
     * @param $keyWord
     */
    public static function sendCustomerMsg($access_token,$openid,$keyWord)
    {
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s',
            $access_token);
        switch ($keyWord['msgType']) {
            case 'text':
                $data = self::msgText($openid,$keyWord['content']);
                break;
            case 'image':
                $data = [];
                break;
            case 'news':

        }
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        \Yii::error('data :' . $json);
        $rst = UsualFunForNetWorkHelper::HttpsPost($url,$json);
        \Yii::error('发送客服消息：'.var_export($rst,true));
    }

    /**
     * 返回文本消息格式
     */
    public static function msgText($openid,$content)
    {
        $dataMsg = [
            'touser'=>$openid,
            'msgtype'=>'text',
            'text'=>[
                'content'=>$content
            ]
        ];
        return $dataMsg;
    }

    /**
     * 获取当前公众号缓存数据
     * @return bool|array
     */
    public static function getCacheInfo()
    {
        $cacheInfo = \Yii::$app->cache->get('app_backend_'.\Yii::$app->user->id);
        if($cacheInfo == false){
            return false;
        }
        $rst = json_decode($cacheInfo,true);
        return $rst;
    }
}