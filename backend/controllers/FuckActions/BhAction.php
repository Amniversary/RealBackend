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
use backend\business\ImageUtil;
use backend\business\JobUtil;
use backend\business\WeChatUserUtil;
use backend\business\WeChatUtil;
use backend\components\MessageComponent;
use callmez\wechat\sdk\MpWechat;
use callmez\wechat\sdk\Wechat;
use common\components\OssUtil;
use common\components\SystemParamsUtil;
use common\components\UsualFunForNetWorkHelper;
use common\models\AuthorizationList;
use common\models\AuthorizationMenu;
use common\models\AuthorizationMenuSon;
use common\models\QrcodeImg;
use common\models\StatisticsCount;
use common\models\User;

use Qiniu\Auth;
use udokmeci\yii2beanstalk\Beanstalk;
use yii\base\Action;
use yii\db\Query;
use yii\web\Cookie;

class BhAction extends Action
{
    public function run()
    {
        echo "<pre>";
        $sta  = microtime(true);
        $data= [
            'ToUserName'=>'gh_7318204277d9',
            'FromUserName'=>'oB4Z-wf0FYMlI7fW4ZvD90Y06RxA',
            'CreateTime'=>'1500542620',
            'MsgType'=>'event',
            'Event'=>'CLICK',
            'EventKey'=>'get_qrcode',
            'appid'=>'wx1024c6215af20360',
        ];
        $openid = $data['FromUserName'];
        $auth = AuthorizerUtil::getAuthOne($data['appid']);
        $access_token = $auth->authorizer_access_token;
        $client = AuthorizerUtil::getUserForOpenId($openid,$auth->record_id);
        $img = ImageUtil::GetQrcodeImg($client->client_id);

        if(!isset($img)) {  //TODO: 如果图片不存在  重新生成并上传
            $q = microtime(true);
            $userData = WeChatUserUtil::getUserInfo($access_token,$openid);
            if(!WeChatUserUtil::getQrcodeSendImg($access_token,$openid,$userData['headimgurl'],$qrcode_file,$pic_file)) {
                print_r('不行');
                exit;
            }
            echo "获取2张图片";
            print_r(microtime(true) - $q);echo "<br/>";
            print_r(microtime(true) - $sta);echo "<br/>";
            $text = $userData['nickname'];
            $w = microtime(true);
            if(!ImageUtil::imagemaking($qrcode_file,$pic_file,$text,$bg_img,$error)){
                \Yii::error('generate Qrcode Img Error: ',var_export($error,true));
                print_r($error);
                exit;
            }
            echo "组装图片";
            print_r(microtime(true) - $w);echo "<br />";
            print_r(microtime(true) - $sta);echo "<br />";
            $name = basename($bg_img);
            $e = microtime(true);
            if(!OssUtil::UploadQiniuFile($name,$bg_img,$bg_url,$error)){  //TODO: 背景图上传七牛
                print_r($error);
                exit;
            }
            echo "上传七牛 ";
            print_r(microtime(true) - $e ); echo "<br />";
            print_r(microtime(true) - $sta);echo "<br />";
            $wechat = new WeChatUtil();
            $r = microtime(true);
            if(!$wechat->Upload($bg_img,$access_token,$rst,$error)) { //TODO: 背景图上传微信素材
                print_r($error);
                exit;
            }
            echo "上传微信";
            print_r(microtime(true) - $r ); echo "<br />";
            print_r(microtime(true) - $e ); echo "<br />";
            /*$model = new QrcodeImg();
            $model->client_id = $client->client_id;
            $model->media_id = $rst['media_id'];
            $model->pic_url = $bg_url;
            $model->update_time = $rst['created_at'];
            $model->save();*/
            $media_id =  '';//$model->media_id;
            @unlink($qrcode_file);
            @unlink($pic_file);
        } else {
            $time = time();
            $outTime = intval(($time - $img->update_time) / 84600);
            if($outTime >= 3){
                $rst = (new WeChatUtil())->UploadWeChatImg($img->pic_url,$access_token);
                $img->media_id = $rst['media_id'];
                $img->update_time = $rst['created_at'];
                $img->save();
            }
            $media_id = $img->media_id;
        }

        print_r($media_id);
        $end = microtime(true);
        $rst = $end-$sta;
        echo "<br />";
        print_r($rst);
        exit;
        $msgObj = new MessageComponent($this->data);
        $msgData[] = [
            'msg_type'=>'2',
            'media_id'=>$media_id,
        ];
        $msgObj->sendMessageCustom($msgData,$openid);


        exit;
        phpinfo();
        exit;
        $stat = microtime(true);

        $data = [
            'nick_name'=>'Gavean',
            'pic'=>'http://wx.qlogo.cn/mmopen/UVzXBswyibFh7ib0qClxDP6Y5EFUGSgrw7FIUNcB7K60LAIpKHpqHxJa7ta10HKYYIVSCPSQy0IBzGib9zgn9NE00vaHbVydjpY/0',
        ];
        $qrcode = 'http://mmbiz.qpic.cn/mmbiz_jpg/6SPlDzxhRsQZgoUE4507ibia0hcWdicibxPLU2JvGjreoJMA9JDzyQK1IFNQb7OrZDx0HsIjgfuL2pJQe4PXrzIUdg/0';
        $rst = UsualFunForNetWorkHelper::HttpGetImg($data['pic'],$content_type,$error);
        if(!$rst) {
            echo "rst :"."<br />";
            print_r($error);exit;
        }
        $res = UsualFunForNetWorkHelper::HttpGet($qrcode);
        if(!$res) {
            echo "res:";
            print_r($error);exit;
        }
        $text = $data['nick_name'];
        $time = time();
        $filename = \Yii::$app->basePath.'/web/wswh/img/pic_'.$time.'.png';
        $qrcodename = \Yii::$app->basePath.'/web/wswh/img/qrcode_'.$time.'.png';
        file_put_contents($filename,$rst);
        file_put_contents($qrcodename,$res);

        if(!ImageUtil::imagemaking($qrcodename,$filename,$text,$faaa,$error)){
            print_r($error);exit;
        }

        @unlink($filename);
        @unlink($qrcodename);
        $end = microtime(true);
        $return = $end - $stat;
        echo "<br/>";
        print_r($return);
        exit;

        if(!function_exists('imagecreatefromjpeg')){
            echo "no";exit;
        }
        echo "ok";

        exit;
        $rst = \Yii::$app->basePath.'/web/wswh/img/121e21.jpg';
        $sss = basename($rst);
        print_r($sss);
        exit;
        $userData = WeChatUserUtil::getUserInfo('DRT1eWCeFYoCIn9rm6PvCogwtq0N_clZGYO0di145z-t720_8Lyv2FqyTTsH5bIMEWl8GHLvEMc2GV7pGFUdc-QWkoUcIysViE6KS2_aIL2QbYmeuIliON_mlT4Iv4RyCXQbADDWRK','oB4Z-wf0FYMlI7fW4ZvD90Y06RxA');
        print_r($userData);
        exit;
        $msg = SystemParamsUtil::GetSystemParam('qrcode_msg',true,'value1');
        $rst =  sprintf($msg,(5-3));
        print_r($rst);
        exit;
        $num = 1;
        ++ $num;
        print_r($num);
        exit;
        if(!WeChatUserUtil::getWxFansAccumulate('OHa-A3h6P8WswEjgTAPt3YST5q-wqgBwCyYAcEU_JQ752xyO5WHxdKkF5wybPgHD_Of2Fhkdj5Hn0B4TOfrHOyAqfFUVJ-HkhNh0x3T39gZDKyMkM21loCVFre1BJV1pTETfAGDLWL',$rst,$error)){
            echo "$error \n";
            exit;
        }
        print_r($rst);
        exit;
        $num = AuthorizerUtil::getAttention(3);
        print_r($num);
        exit;
        $data = ['EventKey' => 'qrscene_oB4Z-wf0FYMlI7fW4ZvD90Y06RxA'];
        if(strpos($data['EventKey'],'qrscene_') !== false) {
            $str = str_replace('qrscene_','',$data['EventKey']);
            $str = trim($str);
        }

        exit;
        $srt = microtime(true);
/*
Array
(
    [ticket] => gQGO7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTUh6THc3NzZkLTExbnJWSnhwY00AAgSveG1ZAwQsAQAA
    [expire_seconds] => 300
    [url] => http://weixin.qq.com/q/02MHzLw776d-11nrVJxpcM
)*/
        $auth = AuthorizerUtil::getAuthByOne(3);
        $rst = WeChatUserUtil::getQrcodeTickt($auth->authorizer_access_token,$error);
        if(!$rst) {
            print_r($error);exit;
        }
        if(!WeChatUserUtil::getQrcodeImg($rst['ticket'],$file)){
            exit('no');
        }
        $end = microtime(true);
        $time = $end -$srt;
        print_r($time);
        print_r($file);
        exit;



        exit;
        imagejpeg($source_image);
        //imagedestroy($source_image);
        exit;
        if(!WeChatUserUtil::getWxFansAccumulate('yrP4t2AdRJ6qrDdfi4Xrg80OLYqlAQF6O3CHC1zFnZnyZy5ctYRDensz_w24hbPKZfCjVYpIBHCFi46scFABRt8eJtNlxMmzdeHwYFu440_Vq6Xn1wO-7-Cf9xgdi7X1ELUfAIDVCL',$rst,$error)){
            print_r($error);
            exit;
        }
        print_r($rst);
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