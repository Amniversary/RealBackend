<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/6
 * Time: 下午5:27
 */

namespace backend\controllers\FuckActions;


use backend\business\AuthorizerUtil;
use backend\business\DailyStatisticUsersUtil;
use backend\business\JobUtil;
use backend\business\WeChatUserUtil;
use backend\business\WeChatUtil;
use backend\components\MessageComponent;
use callmez\wechat\sdk\MpWechat;
use callmez\wechat\sdk\Wechat;
use common\components\UsualFunForNetWorkHelper;
use common\models\AuthorizationList;
use common\models\AuthorizationMenu;
use common\models\AuthorizationMenuSon;
use common\models\StatisticsCount;
use common\models\User;

use Qiniu\Auth;
use udokmeci\yii2beanstalk\Beanstalk;
use yii\base\Action;
use yii\db\Query;

class BhAction extends Action
{
    public function run()
    {
        echo "<pre>";
        $data  = [1,2,3,4,5,6];
        foreach($data as $item){
            print_r($item);
            break;
        }

        exit;
        $data = ['appid'=>'wx1024c6215af20360'];
        $msgData = new MessageComponent($data,0);
        $sr = $msgData->getMessageModel();
        print_r($sr);
        exit;
        $AuthInfo = AuthorizerUtil::getAuthOne('wxfb4431191609bd1e');
        print_r($AuthInfo);
        exit;
        //TODO: 处理回复消息逻辑 走客服消息接口 回复多条消息
        $auth = AuthorizationList::findOne(['record_id'=>3]);
        $item = [
            'msg_type'=>'2',
            'media_id'=>'sRc34mo9KOo4XiPGhZ33oivTle80mXAM2cWDn96XqU7krMdSmkDM8Kg1_uMBADBb',
        ];
        $paramData  = [
            'key_word'=>'wechat',
            'open_id'=>'oB4Z-wf0FYMlI7fW4ZvD90Y06RxA',
            'authorizer_access_token'=>$auth->authorizer_access_token,
            'item'=>$item
        ];
        if(!JobUtil::AddCustomJob('wechatBeanstalk','wechat',$paramData,$error)){
            print_r($error);
            exit;
        }
        echo "ok";
        exit;
        //$msgData = AuthorizerUtil::getAttentionMsg(3,1,11);
        $auth = AuthorizationList::findOne(['record_id'=>3]);
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s',
            $auth->authorizer_access_token);
        $openid = 'oB4Z-wf0FYMlI7fW4ZvD90Y06RxA';
        $data = [
            'touser'=>$openid,
            'msgtype'=>'text',
            'text'=>[
                'content'=>'eeee'
            ]
        ];
        $data = [
            'touser'=>$openid,
            'msgtype'=>'news',
            'news'=>[
                'articles'=>[[
                    'title'=>'qqq',
                    'description'=>'sasaaaa',
                    'url'=>'www.aaa.com',
                    'picurl'=>'qwqewqewq',
                ]
                ]
            ],
        ];
        $data = [
            'touser'=>$openid,
            'msgtype'=>'image',
            'image'=>[
                'media_id'=>'sRc34mo9KOo4XiPGhZ33oivTle80mXAM2cWDn96XqU7krMdSmkDM8Kg1_uMBADBb'
            ]
        ];
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$json),true);
        print_r($rst);
        exit;
        //\Yii::error('curlData::'.var_export($data,true));


        $wx = new WeChatUtil();
        $rst = $wx->UploadWeChatImg('http://7xld1x.com1.z0.glb.clouddn.com/headbg1.jpg','E1APCI4AD4kk4kgjQybt6wwEQ9IM0Qy5wPa0hUFJVFRHO6Jok12MUwvcljKOI9BkElnXWXPQMbc1v8Y3qWCK10axeNf7n9nmSVvhG_uHmCRRapAWnWKbHUQxO8K80IBTYGSfAKDCKW');
        print_r($rst);
        exit;
        $wx = new WeChatUtil();
        $rst = $wx->UploadWeChatImg('http://7xld1x.com1.z0.glb.clouddn.com/headbg1.jpg');
        print_r($rst);
        exit;
        //      增加消息队列
        if(!JobUtil::AddCustomJob('beanstalk','tube','',$error)){
            print_r($error);
            exit;
        }
        echo "ok";
        exit;
        $params = \Yii::$app->params['QiNiuOss'];
        $auth = new Auth($params['ak'],$params['sk']);
        $bucket = $params['bucket'];
        print_r($bucket);exit;
        $token = $auth->uploadToken($bucket);
        $cache = WeChatUserUtil::getCacheInfo();
        $rst = WeChatUserUtil::getWxFansAccumulate($cache['authorizer_access_token']);
        $res = WeChatUserUtil::getWxFansSummary($cache['authorizer_access_token']);
        print_r($rst);
        print_r($res);

        exit;

        $rst = date('Y-m-d H:i:s',1499676884);
        print_r($rst);
        exit;
        $query = (new Query())->select(['backend_user_id'])->from('wc_user')->all();
        print_r($query);
        exit;
        $rst = AuthorizerUtil::getMenuCount(3);
        print_r($rst);
        exit;
        $time = intval((time() - '') / 84600);
        print_r($time);
        exit;
        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $query = (new Query())->select(['menu_id'])->from('wc_authorization_menu')->where(['is_list'=>1])->all();
        AuthorizationMenu::deleteAll(['app_id'=>$cacheInfo['record_id']]);
        foreach ($query as $v){
            AuthorizationMenuSon::deleteAll(['menu_id'=>$v['menu_id']]);
        }
        exit;

        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $access_token = $cacheInfo['authorizer_access_token'];
        $url = "https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token=$access_token";
        $rst = UsualFunForNetWorkHelper::HttpGet($url);
        $rst = json_decode($rst,true);
        if($rst['errcode'] != 0){
            print_r($rst);
            exit;
        }
        $data = $rst['selfmenu_info']['button'];
        foreach ($data as $item){
            $model = new AuthorizationMenu();
            $model->app_id = $cacheInfo['record_id'];
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
        print_r($rst);
        exit;

        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $query = (new Query())
            ->select(['menu_id','app_id','name','ifnull(type,\'\') as type','ifnull(key_type,\'\') as key_type','is_list'])
            ->from('wc_authorization_menu')
            ->where('app_id = 3')->all();
        //print_r($query);

        $data = [];
        foreach ($query as $key => $v){
            if(!$v['is_list']){
                $data['button'][$key] = $v['type'] == 'click' ? ['key'=>$v['key_type']] :['url'=>$v['key_type']];
                $data['button'][$key]['name'] = $v['name'];
                $data['button'][$key]['type'] = $v['type'];
            }else{
                $sql = (new Query())
                    ->select(['name','type','url','key_type'])
                    ->from('wc_authorization_menu_son')
                    ->where('menu_id = :md',[':md'=>$v['menu_id']])->all();
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
        print_r($data);
        $access_token = $cacheInfo['authorizer_access_token'];
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$access_token";
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$json),true);
        print_r($rst);
        exit;

        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $data = [
            'button'=>[
                [
                    'type'=>'click',
                    'name'=>'点击打开',
                    'key'=>'open',
                ],
                [
                    'name'=>'菜单',
                    'sub_button'=>[
                        [
                            'type'=>'view',
                            'name'=>'百度',
                            'url'=>'http://www.baidu.com',
                        ],
                        [
                            'type'=>'click',
                            'name'=>'点一下赞',
                            'key'=>'click',
                        ]
                    ]
                ],
                [
                    'name'=>'其他',
                    'sub_button'=>[
                        [
                            'type'=>'view',
                            'name'=>'帮助中心',
                            'url'=>'http://mibo.matewish.cn/mibo/help.html',
                        ]
                    ]
                ]
            ]
        ];
        $access_token = $cacheInfo['authorizer_access_token'];
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$access_token";
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$json),true);
        print_r($rst);

        exit;






        exit;
        $url = 'http://7xld1x.com1.z0.glb.clouddn.com/858390311.jpg';
        $WeChat = new WeChatUtil();
        $rst = $WeChat->UploadWeChatImg($url);

    }
}