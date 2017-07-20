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
use yii\base\Exception;
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
        return json_decode(UsualFunForNetWorkHelper::HttpGet($url),true);
    }

    /**
     * 发送客服消息
     * @param $access_token
     * @param $json
     * @return bool
     */
    public static function sendCustomerMsg($access_token,$json)
    {
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s',
            $access_token);
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$json),true);
        return !isset($rst['errcore']) && $rst['errmsg'] == 'ok' ? true:$rst;
    }


    /**
     * 设置微信菜单
     * @param $access_token
     * @param $data
     * @return array
     */
    public static function setCustomMenu($access_token,$data)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$access_token";
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        return json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$json),true);
    }


    /**
     * 获取微信自定义菜单配置
     * @param $access_token
     * @param $error
     * @return bool|array
     */
    public static function getCustomMenu($access_token,&$error){
        $url = "https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token=$access_token";
        $rst = json_decode(UsualFunForNetWorkHelper::HttpGet($url),true);
        if($rst['errcode'] != 0){
            $error = '获取微信自定义菜单列表失败：Code：'.$rst['errcode'] . ' Msg：'. $rst['errmsg'];
            return false;
        }
        return $rst;
    }
    /**
     * 配置消息模版
     * @param $msgData
     * @param $openid
     * @return string
     */
    public static function getMsgTemplate($msgData,$openid)
    {
        //TODO: 0 文本消息 1 图文消息 2 图片消息
        $data = '';
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
        return json_encode($data,JSON_UNESCAPED_UNICODE);
    }


    /**
     * 图文消息模版
     */
    public static function msgNews($openid,$msgData)
    {
        unset($msgData['msg_type']);
        return $data = [
            'touser'=>$openid,
            'msgtype'=>'news',
            'news'=>[
                'articles'=>$msgData
            ],
        ];
    }

    /**
     * 返回文本消息格式
     */
    public static function msgText($openid,$content)
    {
        return $dataMsg = [
            'touser'=>$openid,
            'msgtype'=>'text',
            'text'=>[
                'content'=>$content
            ]
        ];
    }

    /**
     * 返回图片消息类型
     */
    public static function msgImage($openid,$media_id)
    {
        return $dataMsg = [
            'touser'=>$openid,
            'msgtype'=>'image',
            'image'=>[
                'media_id'=>$media_id
            ]
        ];
    }


    /**
     * 获取当前公众号缓存数据
     * @return bool|array
     */
    public static function getCacheInfo()
    {
        $cacheInfo = \Yii::$app->cache->get('app_backend_'.\Yii::$app->user->id);
        if($cacheInfo == false)
            return false;
        return json_decode($cacheInfo,true);
    }

    /**
     * 删除自定义菜单
     */
    public static function DeleteCustom()
    {
        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $query = (new Query())->select(['menu_id'])->from('wc_authorization_menu')->where(['is_list'=>1])->all();
        AuthorizationMenu::deleteAll(['app_id'=>$cacheInfo['record_id']]);
        foreach ($query as $v){
            AuthorizationMenuSon::deleteAll(['menu_id'=>$v['menu_id']]);
        }
    }

    /**
     * 获取微信配置自定菜单
     * @param $access_token
     * @param $app_id
     * @return bool
     * @throws HttpException
     */
    public static function getAppMenus($access_token,$app_id)
    {
        self::DeleteCustom();
        $rst = self::getCustomMenu($access_token,$error);
        if(!$rst) throw new HttpException(500,$error);
        if($rst['is_menu_open'] == 0) throw new HttpException(500,'自定义菜单未开启');
        $data = $rst['selfmenu_info']['button'];
        if(empty($data)){
            throw new HttpException(500,'自定义菜单列表为空');
        }

        $trans = \Yii::$app->db->beginTransaction();
        try{
            foreach ($data as $item) {
                $model = new AuthorizationMenu();
                $model->app_id = $app_id;
                if (!isset($item['sub_button'])) {
                    $model->type = $item['type'];
                    $model->name = $item['name'];
                    $model->key_type = isset($item['key']) ? $item['key'] : '';
                    $model->is_list = 0;
                    if (!$model->save()) throw new HttpException(500, '保存一级菜单信息失败');
                } else {
                    $model->is_list = 1;
                    $model->name = $item['name'];
                    $model->save();
                    foreach ($item['sub_button']['list'] as $v) {
                        $list = new AuthorizationMenuSon();
                        $list->menu_id = $model->menu_id;
                        $list->name = $v['name'];
                        $list->key_type = isset($v['key']) ? $v['key'] : '';
                        $list->url = isset($v['url']) ? $v['url'] : '';
                        $list->type = $v['type'];
                        if (!$list->save()) throw new HttpException(500, '保存二级菜单信息失败');
                    }
                }
            }
            $trans->commit();
        }catch (Exception $e){
            $trans->rollBack();
            return false;
        }
        return true;
    }

    /**
     * 设置微信菜单
     */
    public static function  setMenuList($query,$access_token,&$error)
    {
        $data = [];
        foreach ($query as $key => $v){
            if(!$v['is_list']){
                $data['button'][$key] = $v['type'] == 'click' ? ['key'=>$v['key_type']] :['url'=>$v['key_type']];
                $data['button'][$key]['type'] = $v['type'];
                $data['button'][$key]['name'] = $v['name'];
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
                    $info[$q]['type'] = $value['type'];
                    $info[$q]['name'] = $value['name'];

                }
                $data['button'][$key]['sub_button'] = $info;
            }
        }
        return WeChatUserUtil::setCustomMenu($access_token,$data);
    }

    /**
     * 获取累积粉丝数
     * @param $access_token
     * @return bool
     */
    public static function getWxFansAccumulate($access_token,&$rst,&$error)
    {
        $url = "https://api.weixin.qq.com/datacube/getusercumulate?access_token=$access_token";
        $data['begin_date'] = date('Y-m-d',strtotime('-1 day'));;
        $data['end_date'] = date('Y-m-d',strtotime('-1 day'));;
        $json = json_encode($data);
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$json),true);
        if($rst['errcode'] != 0){
            $error = 'errcode: '.$rst['errcode'].' errmsg: '. $rst['errmsg'];
            return false;
        }
        return true;
    }

    /**
     * 获取粉丝数增减数据
     * @param $access_token
     * @return mixed
     */
    public static function getWxFansSummary($access_token)
    {
        $url = "https://api.weixin.qq.com/datacube/getusersummary?access_token=$access_token";
        $data['begin_date'] = date('Y-m-d',strtotime('-1 day'));
        $data['end_date'] = date('Y-m-d',strtotime('-1 day'));
        $json = json_encode($data);
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$json),true);
        if($rst['errcode'] != 0){
            print_r($rst);
            exit;
        }
        return $rst;
    }


    /**
     * 获取临时二维码Ticket 参数
     * @param $access_token  //授权access_token
     * @param $expire_seveonds  //二维码过期时间
     * @param $action_name  //二维码请求类型参数值  [
     *                          QR_SCENE临时整型
     *                          QR_STR_SCENE 临时字符型
     *                          QR_LIMIT_SCENE永久整型
     *                          QR_LIMIT_STR_SCENE永久字符型
     *                      ]
     * @param $action_info  //二维码详细信息
     * @param $error
     * @return [
     *              ticket=>'',           获取二维码的Ticket
     *              expire_seconds=>'',   二维码的有效期限 // 临时型二维码时需要
     *              url=>''               二维码解析后的地址, 可以使用自行生成二维码
     *          ]
     *
     */
    public static function getQrcodeTickt($access_token,$openid,&$error,$actionName = 'QR_LIMIT_STR_SCENE',$expire = 7200)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$access_token";
        $data = [
            'action_name'=>$actionName,
            'action_info'=>[
                'scene'=>['scene_str'=>$openid],
            ]
        ];
        if(in_array($actionName,['QR_SCENE','QR_STR_SCENE']))
            $data['expire_seconds'] = $expire;
        $json = json_encode($data);
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$json),true);
        if(isset($rst['errcode'])){
            $error = 'Code :'.$rst['errcode'] . ' msg : '. $rst['errmsg'];
            return false;
        }
        return $rst;
    }

    /**
     *  生成二维码本地文件
     * @param $ticket
     * @return bool
     */
    public static function getQrcodeImg($ticket,&$file)
    {
        $ticket =  urlencode($ticket);
        $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$ticket";
        $rst = UsualFunForNetWorkHelper::HttpGet($url);
        if(!$rst) {
            return false;
        }
        $file = \Yii::$app->basePath.'/runtime/source/qrcode_'.time().'.png';
        if(!file_put_contents($file,$rst)) {
            return false;
        }
        return true;
    }

    /**
     * 获取要发送的二维码图片
     * @param $access_token
     * @param $file
     * @return bool
     */
    public static function getQrcodeSendImg($access_token,$openid,$pic,&$qrcode_file,&$pic_file)
    {
        $rst_ticket = self::getQrcodeTickt($access_token,$openid,$error);
        if(!$rst_ticket){
            \Yii::error('get ticket : '.var_export($error,true));
            return false;
        }
        $ticket = $rst_ticket['ticket'];
        if(!self::getQrcodeImg($ticket,$qrcode_file)) {
            \Yii::error('get Qrcode img Error');
            return false;
        }
        $rst = UsualFunForNetWorkHelper::HttpGetImg($pic,$content_type,$error);
        if(!$rst) {
            \Yii::error('Create - Img:'.var_export($error,true));
            return null;
        }
        $pic_file = \Yii::$app->basePath.'/runtime/source/pic_'.time().'.png';
        if(!file_put_contents($pic_file,$rst)) {
            \Yii::error('get Pic img Error');
            return false;
        }
        return true;
    }
}