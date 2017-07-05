<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/1
 * Time: 下午3:55
 */

namespace backend\business;


use common\models\AttentionEvent;
use common\models\AuthorizationList;
use common\models\Client;
use yii\db\Query;

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
     * 获取消息列表
     * @param $id
     * @param $flag
     * @param $key
     * @return array|bool
     */
    public static function getAttentionMsg($id,$flag = 0,$key = null)
    {
        if(!empty($key)){
            $condition = sprintf('app_id=%s and flag=%s and key_id=%s',
                $id,$flag,$key);
        }else{
            $condition = sprintf('app_id=%s and flag=%s',
                $id,$flag);
        }

        $query = (new Query())
            ->select(['app_id','event_id','content','msg_type','title','description','url','picurl'])
            ->from('wc_attention_event')
            ->where($condition)->all();

        if(empty($query)){
            return false;
        }
        $data = [];
        foreach ($query as $list){
            if($list['msg_type'] == 0){
                $data[] = ['content'=>$list['content'],'msg_type'=>$list['msg_type']];
            }
        }
        $articles = [];
        foreach ($query as $item){
            if($item['msg_type'] == 1){
                $articles[$item['event_id']][] = [
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'url' => $item['url'],
                    'picurl' => $item['picurl']
                ];
            }
        }
        if(!empty($articles)){
            foreach ($articles as $key){
                $data[] = $key;
            }
        }
        return $data;
    }

    /**
     * 获取用户基础信息模型
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
            if(!isset($data['open_id'])){
                \Yii::error('not OpenId: '.var_export($data,true));
            }
            $model->open_id = isset($data['open_id'])? $data['open_id'] : '';
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

    /**
     *
     * @param $app_id
     * @return array
     */
    public static function getAppMsg($app_id)
    {
        $query = (new Query())
            ->select(['key_id','keyword','rule'])
            ->from('wc_keywords')
            ->where(['app_id'=>$app_id])->all();
        return $query;
    }
}