<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/1
 * Time: 下午3:55
 */

namespace backend\business;


use common\models\AuthorizationList;
use common\models\Client;

class AuthorizerUtil
{
    /**
     * 根据AppId获取公众号信息
     * @param $appid
     * @return null|AuthorizationList
     */
    public static function getAuthOne($appid)
    {
        return AuthorizationList::findOne(['authorizer_appid'=>$appid]);
    }

    /**
     * 获取微信用户基本信息
     * @param $openid
     * @param $appid
     * @return null|Client
     */
    public static function getAuthOneForOpenId($openid,$appid)
    {
        return Client::findOne(['open_id'=>$openid,'app_id'=>$appid]);
    }

    /**
     * 
     * @param $data
     * @param $app_id
     * @param bool $flag
     * @return Client|null
     */
    public static function genModel($data,$app_id,$flag = false)
    {
        $model = $data;
        if(!$flag){
            $model = new Client();
            $model->open_id = $data['open_id'];
            $model->app_id = $app_id;
            $model->create_time = date('Y-m-d H:i:s');
        }

        $model->subscribe = isset($data['subscribe'])? $data['subscribe'] : '';
        $model->nick_name = isset($data['nickname'])? $data['nickname'] : '';
        $model->sex = isset($data['sex'])? $data['sex'] : '';
        $model->language = isset($data['language'])? $data['language'] : '';
        $model->city = isset($data['city'])? $data['city'] : '';
        $model->country = isset($data['country'])? $data['country'] : '';
        $model->headimgurl = isset($data['headimgurl'])? $data['headimgurl'] : '';
        $model->unionid = isset($data['unionid'])? $data['unionid'] : '';
        $model->remark = isset($data['remark'])? $data['remark'] : '';
        $model->groupid = isset($data['groupid'])? $data['groupid'] : '';
        $model->province = isset($data['province'])? $data['province'] : '';
        $model->subscribe_time = isset($data['subscribe_time'])? $data['subscribe_time'] : '';
        $model->update_time = date('Y-m-d H:i:s');
        return $model;
    }
}