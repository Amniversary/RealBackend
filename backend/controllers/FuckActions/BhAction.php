<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/6
 * Time: 下午5:27
 */

namespace backend\controllers\FuckActions;


use backend\business\AuthorizerUtil;
use backend\business\WeChatUserUtil;
use backend\business\WeChatUtil;
use common\components\UsualFunForNetWorkHelper;
use common\models\AuthorizationMenu;
use common\models\AuthorizationMenuSon;
use yii\base\Action;
use yii\db\Query;

class BhAction extends Action
{
    public function run()
    {
        echo "<pre>";
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