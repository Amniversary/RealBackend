<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/1
 * Time: 下午3:08
 */

namespace backend\business;


use common\components\UsualFunForNetWorkHelper;
use common\models\AuthorizationMenu;
use common\models\AuthorizationMenuSon;
use yii\db\Query;
use yii\web\HttpException;

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
     * @param $msgData
     */
    public static function sendCustomerMsg($access_token,$openid,$msgData)
    {
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s',
            $access_token);

        switch ($msgData['msg_type']) {
            case '0':
                $data = self::msgText($openid,$msgData['content']);
                break;
            case '1':
                $data = self::msgNews($openid,$msgData);
                break;
            case '2':
                $data = self::msgImage($openid,$msgData['media_id']);
                break;
        }
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        \Yii::error('data :' . $json);
        $rst = UsualFunForNetWorkHelper::HttpsPost($url,$json);
        \Yii::error('发送客服消息：'.var_export($rst,true));
    }

    /**
     * 图文消息模版
     */
    public static function msgNews($openid,$msgData)
    {
        unset($msgData['msg_type']);
        $data = [
            'touser'=>$openid,
            'msgtype'=>'news',
            'news'=>[
                'articles'=>$msgData
            ],
        ];
        return $data;
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
     * 返回图片消息类型
     */
    public static function msgImage($openid,$media_id)
    {
        $dataMsg = [
            'touser'=>$openid,
            'msgtype'=>'image',
            'image'=>[
                'media_id'=>$media_id,
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


    public static function DeleteCustom()
    {
        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $query = (new Query())->select(['menu_id'])->from('wc_authorization_menu')->where(['is_list'=>1])->all();
        AuthorizationMenu::deleteAll(['app_id'=>$cacheInfo['record_id']]);
        foreach ($query as $v){
            AuthorizationMenuSon::deleteAll(['menu_id'=>$v['menu_id']]);
        }
    }

    public static function getAppMenus($access_token,$app_id)
    {
        self::DeleteCustom();
        $url = "https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token=$access_token";
        $rst = UsualFunForNetWorkHelper::HttpGet($url);
        $rst = json_decode($rst,true);
        if($rst['errcode'] != 0){
            throw new HttpException(500,'获取菜单配置规则失败 Code:'.$rst['errcode'].' Msg:'.$rst['errmsg']);
        }
        $data = $rst['selfmenu_info']['button'];
        foreach ($data as $item){
            $model = new AuthorizationMenu();
            $model->app_id = $app_id;
            if(!isset($item['sub_button'])){
                $model->type = $item['type'];
                $model->name = $item['name'];
                $model->key_type = isset($item['key']) ? $item['key']:'';
                $model->is_list = 0;
                $model->save();
            }
            else
            {
                $model->is_list = 1;
                $model->name = $item['name'];
                $model->save();
                foreach ($item['sub_button']['list'] as $v){
                    $list = new AuthorizationMenuSon();
                    $list->menu_id = $model->menu_id;
                    $list->name = $v['name'];
                    $list->key_type = isset($v['key']) ? $v['key']: '';
                    $list->url = isset($v['url']) ?$v['url']: '';
                    $list->type = $v['type'];
                    $list->save();
                }
            }
        }
    }

    /**
     * 设置微信菜单
     */
    public static function setMenuList($query,$access_token,&$error)
    {
        $data = [];
        foreach ($query as $key => $v){
            if(!$v['is_list']){
                $data['button'][$key] = $v['type'] == 'click' ? ['key'=>$v['key_type']] :['url'=>$v['key_type']];
                $data['button'][$key]['name'] = $v['name'];
                $data['button'][$key]['type'] = $v['type'];
            }else{
                $sql = AuthorizerUtil::getMenuSonList($v['menu_id']);
                if(empty($sql)){
                    $error = '没有找到二级菜单信息，菜单名称：'.$v['name'];
                    return false;
                }
                $data['button'][$key] = [
                    'name'=>$v['name'],
                ];
                $info = [];
                foreach ($sql as $q => $value){
                    $info[$q] = $value['type'] == 'click'? ['key'=>$value['key_type']]:['url'=>$value['url']];
                    $info[$q]['name'] = $value['name'];
                    $info[$q]['type'] = $value['type'];
                }
                $data['button'][$key]['sub_button'] = $info;
            }
        }
       \Yii::error('msg:'.var_export($data,true));
        exit;
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$access_token";
        $res = json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$json),true);
        return $res;
    }
}