<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/21
 * Time: 14:27
 */

namespace frontend\business;



use common\components\QrCodeUtil;
use common\models\AuthorizationList;
use common\models\Client;
use yii\db\Query;


class ClientUtil
{
    /**
     * 根据AppId获取公众号信息
     * @param $appid
     * @return null|AuthorizationList
     */
    public static function getAuthOne($appid)
    {
        return AuthorizationList::findOne(['authorizer_appid'=>$appid,'status'=>1]);
    }

    /**
     * 获取微信用户基本信息
     * @param $openid
     * @param $appid
     * @return null|Client
     */
    public static function getUserForOpenId($openid,$appid)
    {
        return Client::findOne(['open_id'=>$openid,'app_id'=>$appid]);
    }

    /**
     * @param $user_id
     * @param $app_id
     * @return array
     */
    public static function getAttensionList($user_id,$app_id)
    {
        $query = (new Query())->from('wc_qrcode_share qs')->innerJoin('wc_client t','qs.other_user_id=t.client_id')
            ->select(['t.client_id as id','nick_name','headimgurl as pic','qs.create_time'])
            ->where(['share_user_id'=>$user_id,'qs.app_id'=>$app_id])->all();
        if(empty($query)){
            return [];
        }
        $data = [];
        foreach($query as $item=>$value){
            $data[$item]['id'] = intval($value['id']);
            $data[$item]['nick_name'] = $value['nick_name'];
            $data[$item]['pic'] = $value['pic'];
            $data[$item]['create_time'] = $value['create_time'];
        }


        return $data;
    }

} 