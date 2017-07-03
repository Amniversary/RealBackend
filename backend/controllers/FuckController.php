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
use common\components\UsualFunForNetWorkHelper;
use common\models\Client;
use common\models\Menu;
use common\models\User;
use common\models\UserMenu;
use yii\db\Query;
use yii\web\Controller;
class FuckController extends Controller
{
    public function actionIndex()
    {
        echo "<pre>";
        $key = 'user_menu_1';
        $cnt = \Yii::$app->cache->get($key);
        $key2 = 'user_power_1';
        $cnt1 = \Yii::$app->cache->get($key2);
        print_r(json_decode($cnt,true));
        print_r(json_decode($cnt1,true));
        exit;
        echo "<pre>";
        $rst = AuthorizerUtil::getAttentionMsg(1);
        print_r($rst);
        exit;
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

    public function actionDelete()
    {




        exit;
        \Yii::$app->cache->delete('app_backend_1');
        echo 'OK';
    }

    public function actionSet()
    {
        $json = \Yii::$app->cache->get('app_backend_1');
        $cache = json_decode($json,true);
        var_dump($cache);
    }
    public function actionBh(){
        echo "<pre>";
        $appid = 'wx1024c6215af20360';
        $openid = 'oB4Z-wf0FYMlI7fW4ZvD90Y06RxA';
        $openInfo = AuthorizerUtil::getAuthOne($appid);
        $access_token = $openInfo->authorizer_access_token;
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s',
            $access_token);

        $data = WeChatUserUtil::msgNews($openid);
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        print_r($data);
        echo "<br />";
        $rst = UsualFunForNetWorkHelper::HttpsPost($url,$json);
        echo "发送客服消息：";
        print_r($rst);

        exit;
        $sql = '';
        $str1= '11';
        $str2='22';
        $table = \Yii::$app->db->tablePrefix;
        $sql .= sprintf('insert into %s_user_menu(user_id,menu_id) values(%s,%s);',$table,$str1,$str2);
        print_r($sql);
    }
}