<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/21
 * Time: 上午10:50
 */

namespace backend\controllers;


use backend\business\AuthorizerUtil;
use backend\business\UserMenuUtil;
use backend\business\WeChatUserUtil;
use backend\business\WeChatUtil;
use common\components\Des3Crypt;
use common\components\OssUtil;
use common\components\UsualFunForNetWorkHelper;
use common\models\Client;
use common\models\Keywords;
use common\models\Menu;
use common\models\User;
use common\models\UserMenu;

use frontend\business\ClientUtil;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use yii\db\Query;
use yii\web\Controller;
use yii\web\Cookie;

class FuckController extends Controller
{

    public $enableCsrfValidation = false;

    public function actions()
    {
        return require (__DIR__.'/FuckActions/FuckActionConfig.php');
    }

    public function actionDemo(){
        \Yii::error(1);
        return $this->render('demo');
    }
    public function actionDemoa(){
        \Yii::error('file'.var_export($_FILES));
        $rst = ['code'=>1,'msg'=>''];
        echo json_encode($rst);
        exit;
    }
    public function actionIndex()
    {
        echo "<pre>";
        $rst = AuthorizerUtil::getAttentionMsg(3);
        if(empty($rst)){
            exit('没有数据');
        }
        print_r($rst);
        exit;
        \Yii::$app->cache->delete('app_backend_1');
        exit;
        $key = 'user_menu_1';
        $cnt = \Yii::$app->cache->get($key);
        $key2 = 'user_power_1';
        $cnt1 = \Yii::$app->cache->get($key2);
        print_r(json_decode($cnt,true));
        print_r(json_decode($cnt1,true));
        exit;
        echo "<pre>";

        $content = '换一你过嘎洒点';
        $data = ['msgType'=>'text','content'=>$content];
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);

        print_r($json);
        exit;
        $rst = new WeChatUtil();
        $rst->getAuthorizeInfo('wxfb4431191609bd1e',$out,$error);
        print_r($out);
                exit;


         $access = '9LkcKIAHZRbwkx6pK0CXo3V1NpStvVJI6-5j_yUG0n4ysOMOGs9fprMv7XtET5QFA33WwMqy-oyBhvHoUHmkq2WvRzv7_HrpBCQuCZxJc3GbfOgiffNaBIo8uvU77VB6GHKiAIDWXA';
        $rst = WeChatUserUtil::getUserInfo($access,'oB4Z-wf0FYMlI7fW4ZvD90Y06RxA');//oB4Z-wf0FYMlI7fW4ZvD90Y06RxA
         echo "<br />";
         print_r($rst);
        exit;

        $a = [];
        empty($a) ?  var_dump(1) : var_dump(2) ;

        exit;

        /*var_dump(extension_loaded('mcrypt'));
        phpinfo();
        exit;*/
        header('content-type: text/html; charset=utf-8');
        echo "<pre>";
        $pwd = '75r9UXSpikduk8/EOF8kXA==';
        $len = strlen(strval(time()));
        $key = \Yii::$app->params['pwd_crypt_key'];
        $soucePwd = Des3Crypt::des_decrypt($pwd,$key);
        print_r($soucePwd);
        $soucePwd = substr($soucePwd,0,strlen($soucePwd) - $len);
        echo "<br />";
        print_r($pwd);
        echo "<br />";
        print_r($soucePwd);
    }

    public function actionWxtest(){
        $setMenuPower = (new Query())
            ->select(['mu.menu_id'])
            ->from(Menu::tableName() . 'mu')
            ->innerJoin(UserMenu::tableName(). 'um','mu.menu_id=um.menu_id')
            ->where([
                'url'=>'usermanage/setprivilige',
                'user_id' => 1
            ])->one();
        print_r(!empty($setMenuPower));
    }

    public function actionTest1()
    {
        echo "<pre>";
        $query = (new Query())
            ->select(['app_id','event_id','content','msg_type','title','description','url','picurl'])
            ->from('wc_attention_event')
            ->where(['app_id'=>3,'flag'=>0])->all();
        //print_r($query);
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
        foreach ($articles as $key){
            $data[] = $key;
        }
        print_r($data);
    }

    public function actionSet()
    {
        echo "<pre>";
        $file = 'http://7xld1x.com1.z0.glb.clouddn.com/858390311.jpg';
        $we = new WeChatUtil();
        $we->Upload($file,$rst,$error);
        print_r($rst);
        exit;
        $appid = 'wx1024c6215af20360';
        $openid = 'oB4Z-wf0FYMlI7fW4ZvD90Y06RxA';
        $openInfo = AuthorizerUtil::getAuthOne($appid);
        $access_token = $openInfo->authorizer_access_token;
        $text = '11';
        $query = (new Query())
            ->select(['key_id','keyword','rule'])
            ->from('wc_keywords')
            ->where(['app_id'=>3])->all();
        $flag = null;
        if(!empty($query))
        {
            foreach ($query as $item){
                $flag = false;
                if($item['rule'] == 1){
                    if($text == $item['keyword']) $flag = true;
                }else{
                    if(strpos($item['keyword'],$text) !== false) $flag = true;
                }

                if($flag){
                    //TODO:处理消息回复逻辑
                    $msgData = AuthorizerUtil::getAttentionMsg(3,1,$item['key_id']);
                    print_r($msgData);
                    exit;
                    if(!empty($msgData)){
                        foreach ($msgData as $info){
                            if(!isset($info['msg_type']))
                                $info['msg_type'] = 1;

                            $url = sprintf('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s',
                                $access_token);
                            switch ($info['msg_type']) {
                                case '0':
                                    $data = WeChatUserUtil::msgText($openid,$info['content']);
                                    break;
                                case '1':
                                    $data = WeChatUserUtil::msgNews($openid,$info);
                                    break;
                                case '2':
                                    $data = WeChatUserUtil::msgImage($openid,'');
                                    break;
                            }
                            /*$json = json_encode($data,JSON_UNESCAPED_UNICODE);
                            echo "<br />";
                            print_r($data);
                            $rst = UsualFunForNetWorkHelper::HttpsPost($url,$json);
                            echo "发送客服消息：";
                            print_r($rst);*/
                        }
                    }
                }
            }
        }

    }

    public function actionTest2(){
        //$wechat = new WeChatUtil();
        echo "<pre>";
        //$media_id = 'cpGUzwnhfmo29Am7-3dhpHdqtaUm2KKjaZ4Ybo6GwFgJw6Z-tENbQFMHY4mTUVGw';
        //$access_token = '44tmwzyxd9bSFpTUequlHAjACSAaukCq2-Pp7eYQNg3mY20d_mDMr9XOA0oZcVal2y5a76mzJKI6ycmwhl2pjbvGnqRfCFCAR98m3VAnk1yFRu6NRR-EreOpOzgjQREdQBSeAFAHSJ';
        print_r(\Yii::$app->getBasePath().'/web/wximages/');
        exit;
        $url = "http://7xld1x.com1.z0.glb.clouddn.com/858390311.jpg";
        $file = basename($url);
        $rst = UsualFunForNetWorkHelper::HttpGetImg($url,$content_type,$error);
        if(empty($rst)){
            print_r('error');
            exit;
        }
        //求出文件格式
        $filename = $file;
        $dirname = "./wximages/";
        if(!file_exists($dirname)){
            mkdir($dirname,0777,true);

        }
        file_put_contents($dirname.$filename,$rst);
        print_r($dirname.$filename);



    }

    public function actionTestupload()
    {
        echo "<pre>";
        $file = (__DIR__ . '/20081216112049812011.jpg');
        $items = explode('\\', $file);
       /* Array
        (
            [0] => /Users/a123/PhpstormProjects/version2/RealBackend/backend/controllers/20081216112049812011.jpg
)*/
        $len = count($items);
        $file_name = $items[$len - 1];
        if (strpos($file_name, '.jpg') === false) {
            echo "not .jpg";
            exit;
        }
        $fName = basename($file_name); //20081216112049812011.jpg
        $picUrl = '';
        $error = '';
        // 初始化 UploadManager 对象并进行文件的上传
        if (!OssUtil::UploadQiniuFile($fName, $file, $picUrl, $error)) {
            print_r($error);
            exit;
        }
        print_r($picUrl);

        exit;
        $dir = \Yii::$app->getBasePath().'/web/tttt';
        $picList = [];
        $files = scandir($dir);
        $picStrList = '';
        foreach($files as $file) {
            $items = explode('\\', $file);
            $len = count($items);
            $file_name = $items[$len - 1];
            if (strpos($file_name, '.jpg') === false) {
                echo "not .jpg";
                exit;
            }
            $fName = str_replace('.jpg', '', $file_name);
            $suffix = 'jpg';
            $picUrl = '';
            $error = '';
            //print_r($thumb_files);
            $key = 'user-admin.png';
            // 初始化 UploadManager 对象并进行文件的上传
            if (!OssUtil::UploadQiniuFile($file_name, $file, $picUrl, $error)) {
                return false;
            }
            $picStrList .= $picUrl."\r\n";
            $picList[]=$picUrl;
        }
        var_dump($picList);
        $fileStore = $dir.'/picurl.txt';
        file_put_contents($fileStore,$picStrList);
        exit;
    }

    public function actionBh(){
        echo "<pre>";
        $appid = 'wx1024c6215af20360';
        $openid = 'oB4Z-wUNBqNHzQDbQkjA6XXwOPMg';
        $openInfo = AuthorizerUtil::getAuthOne($appid);
        $access_token = $openInfo->authorizer_access_token;

        $msgInfo = AuthorizerUtil::getAttentionMsg(3);

        foreach ($msgInfo as $info){
            if(!isset($info['msg_type'])){
                $info['msg_type'] = 1;
            }
            $url = sprintf('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s',
                $access_token);
            switch ($info['msg_type']) {
                case '0':
                    $data = WeChatUserUtil::msgText($openid,$info['content']);
                    break;
                case '1':
                    $data = WeChatUserUtil::msgNews($openid,$info);
                    break;
            }
            $json = json_encode($data,JSON_UNESCAPED_UNICODE);
            echo "<br />";
            print_r($data);
            $rst = UsualFunForNetWorkHelper::HttpsPost($url,$json);
            echo "发送客服消息：";
            print_r($rst);

        }


        exit;
        $sql = '';
        $str1= '11';
        $str2='22';
        $table = \Yii::$app->db->tablePrefix;
        $sql .= sprintf('insert into %s_user_menu(user_id,menu_id) values(%s,%s);',$table,$str1,$str2);
        print_r($sql);
    }

    public function actionCookies(){
        /*$cookies = \Yii::$app->response->cookies;

        $cookies->add(new Cookie([
            'name' => 'username',
            'value' => 'yiiuser',
        ]));*/


        //setcookie('username','', time() - 3600);
        print_r(\Yii::$app->session['111']);
    }

}