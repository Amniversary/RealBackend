<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/21
 * Time: 上午10:50
 */

namespace backend\controllers;


use backend\business\UserMenuUtil;
use common\components\Des3Crypt;
use common\models\Menu;
use common\models\User;
use common\models\UserMenu;
use yii\db\Query;
use yii\web\Controller;
class FuckController extends Controller
{
    public function actionIndex()
    {
        phpinfo();
        exit;
        echo "<pre>";
        $key = 'user_menu_1';
        $cnt = \Yii::$app->cache->get($key);
        $key2 = 'user_power_1';
        $cnt1 = \Yii::$app->cache->get($key2);
        print_r(json_decode($cnt,true));
        print_r(json_decode($cnt1,true));
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
        $wechat = \Yii::$app->wechat;
        print_r($wechat->accessToken);
    }

    public function actionTest()
    {

        exit;
        echo "<pre>";
        $pwd = 'admin';
        $pwd = $pwd.strval(time());
        $key = \Yii::$app->params['pwd_crypt_key'];
        $rst = Des3Crypt::des_encrypt($pwd,$key);

        echo $rst;
    }

    public function actionBh(){
        echo "<pre>";
        $key = 'user_menu_1';
        $key2 = 'user_power_1';
        \Yii::$app->cache->delete($key);
        \Yii::$app->cache->delete($key2);
        echo 'OK';

        exit;
        $sql = '';
        $str1= '11';
        $str2='22';
        $table = \Yii::$app->db->tablePrefix;
        $sql .= sprintf('insert into %s_user_menu(user_id,menu_id) values(%s,%s);',$table,$str1,$str2);
        print_r($sql);
    }
}