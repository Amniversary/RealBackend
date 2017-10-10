<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/4
 * Time: 16:55
 */
namespace frontend\controllers\FuckActions;


use backend\business\AuthorizerUtil;
use backend\business\JobUtil;
use backend\business\WeChatUserUtil;
use common\components\IOSBuyUtil;
use common\components\MoneyUtil;
use common\components\mp3\mp3file;
use common\components\PhpLock;
use common\components\SendShortMessage;
use common\components\SystemParamsUtil;
use common\components\tenxunlivingsdk\TimRestApi;
use common\components\UsualFunForNetWorkHelper;
use common\components\UsualFunForStringHelper;
use common\components\wxpay\lib\WxPayOrderQuery;
use common\components\wxpay\lib\WxPayOrderQueryApp;
use common\models\Articles;
use common\models\Books;
use common\models\CAppinfo;
use common\models\Client;
use common\models\Csessioninfo;
use common\models\StatisticsCount;
use common\models\StudyingDynamic;
use common\models\User;
use frontend\api\version\CreateCarousels;
use frontend\api\version\GetUserInfo;
use frontend\api\version\WxUserLogin;
use frontend\business\ClientUtil;
use frontend\business\DynamicUtil;
use frontend\business\RongCloud\UserUtil;

use frontend\business\WeeklyUtil;
use QCloud_WeApp_SDK\Auth\LoginService;
use QCloud_WeApp_SDK\Tunnel\TunnelService;
use yii\base\Action;
use yii\db\Query;
use yii\log\Logger;


class BhAction extends Action
{
    public function run()
    {
        echo "<pre>";

        $sql = 'insert into cClient1 set nickName = \'星泣\', avatarUrl = \'https://wx.qlogo.cn/mmopen/vi_32/0iafBsuyfz4ribGuicA7bLsvhJicUsBjkI5fC2Fqu93OMJibSJ5JwDibBfBO8QOicMcP6U0qJJ0ZJf6ucqXbQglKicQMmg/0\', language = \'zh_CN\', gender = 1, province = \'Zhejiang\', country = \'China\', city =\'Hangzhou\', app_id = \'1\', uuid = \'ce7b024cfe2f050cb54525dd268f3bc0\', skey = \'fa255825433d7a509733b955f974e5c3\', create_time = \'2017-10-09 11:54:15\',last_visit_time = \'2017-10-09 11:54:15\',
open_id = \'oLKwI0bl7BD1VhUCDT9Hv_ohM87I\',session_key = \'dHy33wXvHgMJqxRumgdgpw==\' ,user_info = \'eyJvcGVuSWQiOiJvTEt3STBibDdCRDFWaFVDRFQ5SHZfb2hNODdJIiwibmlja05hbWUiOiLmmJ/ms6MiLCJnZW5kZXIiOjEsImxhbmd1YWdlIjoiemhfQ04iLCJjaXR5IjoiSGFuZ3pob3UiLCJwcm92aW5jZSI6IlpoZWppYW5nIiwiY291bnRyeSI6IkNoaW5hIiwiYXZhdGFyVXJsIjoiaHR0cHM6Ly93eC5xbG9nby5jbi9tbW9wZW4vdmlfMzIvMGlhZkJzdXlmejRyaWJHdWljQTdiTHN2aEppY1VzQmprSTVmQzJGcXU5M09NSmliU0o1SndEaWJCZkJPOFFPaWNNY1A2VTBxSkowWkpmNnVjcVhiUWdsS2ljUU1tZy8wIiwid2F0ZXJtYXJrIjp7InRpbWVzdGFtcCI6MTUwNzUyMTI1NSwiYXBwaWQiOiJ3eGY0ZDY5NTlmMzU5ZjI2YWQifX0=\'
';
        \Yii::$app->db->createCommand($sql)->execute();
        print_r($sql);

        exit;
        $params = [
            'id' => 1,
            'session_key' =>' 1dadas',
            'last_visit_time' =>date('Y-m-d H:i:s'),
            'skey'=>'saasawqeqweqw231321',
            'user_info'=>'wqewqe131321',
            'openid' =>1222112
        ];
        $decode = [
            'nickName' =>'dsadasda',
            'avatarUrl'=>'www.baidu.com',
            'language'=>1,
            'city'=>'hangzhou',
            'gender'=>'1',
            'province'=>'12121',
            'country'=>'country'
        ];
        $sql = 'update cClient'.$params['id'].' set nickName = "'.$decode['nickName'].'",avatarUrl = "'
            .$decode['avatarUrl'].'",language = "'.$decode['language'].'",city = "'.$decode['city']
            .'",gender = "'.$decode['gender'].'",province = "'.$decode['province'].'",country = "'
            .$decode['country'].'",session_key = "'.$params['session_key'].'",last_visit_time = "'
            .$params['last_visit_time'].'",skey = "'.$params['skey'].'",user_info = "'.
            $params['user_info'].'" where open_id = "'.$params['openid'].'";';
        print_r($sql);exit;
        $sql = 'select * from cClient1 where open_id = "212"';
        var_dump(\Yii::$app->db->createCommand($sql)->queryOne());
        exit;
        for($i = 1; $i< 2; $i ++ ) {
        $sql = 'CREATE TABLE IF NOT EXISTS `cClient'. $i .'` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `app_id` int(11) NOT NULL COMMENT \'AppId\',
              `nickName` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `open_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
              `uuid` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
              `avatarUrl` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `language` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `gender` int(11) DEFAULT NULL,
              `province` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `user_info` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
              `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `skey` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
              `session_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
              `create_time` datetime NOT NULL,
              `last_visit_time` datetime NOT NULL,
              `remark1` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `remark2` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `app_id` (`app_id`,`open_id`) USING BTREE,
              KEY `auth` (`uuid`,`skey`),
              KEY `weixin` (`open_id`,`session_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT=\'会话管理用户信息'.$i .'\';
        COMMIT;';
        $rst = \Yii::$app->db->createCommand($sql)->execute();
        var_dump($rst);
        }




        exit;
        $s = 'eyJvcGVuSWQiOiJvTEt3STBWTUlfUmo5ZVNjd0ZlXzRyQk50LW5vIiwibmlja05hbWUiOiJHYXZlYW4iLCJnZW5kZXIiOjEsImxhbmd1YWdlIjoiemhfQ04iLCJjaXR5IjoiSGFuZ3pob3UiLCJwcm92aW5jZSI6IlpoZWppYW5nIiwiY291bnRyeSI6IkNoaW5hIiwiYXZhdGFyVXJsIjoiaHR0cHM6Ly93eC5xbG9nby5jbi9tbW9wZW4vdmlfMzIvUTBqNFR3R1RmVEtRaE83NjdqMHJoazl5a1JhWEs3SUN4R3lld0NNaWFXU2twdm1WRGtlNXZGMDdFSUN0aWJYajVabWZJNU80bmNzT3R2WjFLRkxMcEd1US8wIiwid2F0ZXJtYXJrIjp7InRpbWVzdGFtcCI6MTUwNjM5NTkyNSwiYXBwaWQiOiJ3eGY0ZDY5NTlmMzU5ZjI2YWQifX0=';
        $rst = json_decode(base64_decode($s),true);
        print_r($rst);exit;
        $params = [
            'id'=>1,
            'uuid'=>'121dw12',
            'skey'=>'qwdassdad',
            'create_time'=>date('Y-m-d H:i:s'),
            'last_visit_time'=>date("Y-m-d H:i:s"),
            'openid'=>'121dsad21',
            'user_info' =>'1edsd21',
            'session_key'=>'sadsa',
        ];
        $sql = 'insert into cSessionInfo set app_id = "'.$params['id'].'",uuid = "'.$params['uuid'].'",skey = "' . $params['skey'] . '",create_time = "' . $params['create_time'] . '",last_visit_time = "' . $params['last_visit_time'] . '",open_id = "' . $params['openid'] . '",session_key="' . $params['session_key'] . '",user_info=\''.$params['user_info'].'\'';
        $rst = \Yii::$app->db->createCommand($sql)->execute();
        var_dump($rst);exit;
        print_r(Csessioninfo::find()->where(['app_id'=>3 ,'open_id' => 'oLKwI0VMI_Rj9eScwFe_4rBNt-no'])->one());
        exit;
        $sql = 'select * from cappinfo where appid = :ad';
        $rst = \Yii::$app->db->createCommand($sql,[':ad' => 'wx25d7fec30752314f'])->queryOne();
        print_r($rst);


        exit;
        $select_sql = 'select * from cAppinfo';
        $con = mysql_connect('shanzhuan.mysql.rds.aliyuncs.com:3306', 'hebihan', 'Bh221924');
        if ($con) {
            mysql_select_db('cAuth', $con);
            $arr_result = mysql_query($select_sql);
            mysql_close($con);
            if(mysql_num_rows($arr_result) < 1)
                exit;

            if ($arr_result !== false && !empty($arr_result)) {
                $arresult = array();
                while ($row = mysql_fetch_array($arr_result)) {
                    $arresult['appid'] = $row['appid'];
                    $arresult['secret'] = $row['secret'];
                    $arresult['login_duration'] = $row['login_duration'];
                    $arresult['session_duration'] = $row['session_duration'];
                    $arresult['qcloud_appid'] = $row['qcloud_appid'];
                    $arresult['ip'] = $row['ip'];
                    print_r($arresult);
                }
                exit;
                print_r($arresult);
            } else {
                print_r('no');exit;
            }
        } else {
            print_r("ERROR","$select_sql mysql_connect_err");
            exit;
        }


        exit;
        $json = '{"version":1,"componentName":"MA","interface":{"interfaceName":"qcloud.cam.id_skey","para":{"code":"0011lkux1aZmGf0oT3sx1tBXtx11lkug","encrypt_data":"xufPSGEhtrRzFGUsXtQFdORyVl3rKtmrXG60Tpy+qqHI2tULPDh3lEkTc6oDP3LR1eGKX1eJZje2aHIFlno\/SGBEBNH0UPUSFx9NKUGneylxSro7kTKnkrQTXqL7FpmHSCjvcqWh3mAIs10m8AMoVbmqviHUr5tNZG2zB2i1dLCefFn\/geHDscCL5iyymy2hbY1ykFmekzmxLvJ2uSd3UsNAo2ChMKqmc9WJZW\/NqRNWTt5jeTl9I\/JsnGsy0i3pjNRphbkLBLBLjxXbng5fpXlK+EEoZhd6L9jN7PnznHQKn4Z8vWOewaz6MmyREnFkG3K7LFFw+hvqehDJOWLcERIHG9VoABx6E3M1Ul441uC0KF7Nmyas4DUZJr4XBNCVan0tZHi2IE4FnJkTZYfg\/eDJAvY\/U8TbflCILQjHLg1JymtTBFcv8TlGkTVUe+tjmVzzdd6pWvEYHQPmDfC6kU0KfjRAkDt\/ekgVjjkrmC4=","iv":"6ONs4H\/0K4ja5k9aY0ZK4g=="}}}';
        $rst = json_decode($json,true);
        print_r($rst);

        exit;
        $post  = file_get_contents("php://input");
        \Yii::error('postss:'. $post);
        $header = \Yii::$app->request->headers;
        \Yii::error(var_export($header,true));
        //echo $rst;
        exit;
        $header = \Yii::$app->request->headers;
        \Yii::error('header: '.var_export($header,true));
        $POST = \Yii::$app->request->post();
        \Yii::error('post:'. $POST);
        $data = [$header['OpenId'], $header['AppId']];
        $rst = ['code'=> 0, 'msg'=>'111', 'data'=> $data];
        echo json_encode($rst);

        exit;
        $header = \Yii::$app->response->headers;
        $header->set('ServerName', 'server_user_name');
        $header->set('OpenId', 'ewqjkehk12h3i*&%613h2kj01hjk');

        exit;
        $URL = parse_url('https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKQhO767j0rhk9ykRaXK7ICxGyewCMiaWSkpvmVDke5vF07EICtibXj5ZmfI5O4ncsOtvZ1KFLLpGuQ/0');
        print_r($URL);
        exit;
        $data = [
            'avatarUrl' => 'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKQhO767j0rhk9ykRaXK7ICxGyewCMiaWSkpvmVDke5vF07EICtibXj5ZmfI5O4ncsOtvZ1KFLLpGuQ/0',
            'city' => 'Hangzhou',
            'country' => 'China',
            'gender' => 1,
            'language' => 'zh_CN',
            'nickName' => 'Gavean',
            'openId' => 'oLKwI0VMI_Rj9eScwFe_4rBNt-no',
            'province' => 'Zhejiang',
            'watermark'=> [
                'appid' => 'wxf4d6959f359f26ad',
                'timestamp' =>time()
            ]
        ];
        print_r(TunnelService::emit('7a36c2c8-f092-461e-8a65-46c32d9f35b8', 'system', ['OpenId'=>'111', '22'=>'林镇Sb']));
        //print_r(TunnelService::emit('0299d982-089a-4376-8629-a0244e516b89', 'speak', '6666'));
        //print_r(TunnelService::emit('5a63e852-3321-44f6-be92-719d211bf1ac', 'system', '6666'));
        //print_r(TunnelService::closeTunnel('ce1a0251-6c4a-49b2-8341-3ea227459dd1'));
        exit;
        if (!WeChatUserUtil::WeChatAlarmNotice('数据添加完成', 1, ["Gavean"])) {
            return false;
        }




        exit;
        $content = StudyingDynamic::findOne(['dynamic_id'=>45])['content'];
        $content = trim($content); //清除字符串两边的空格
        $content = str_replace('webp', 'jpg', $content);
        $content = preg_replace('/\s\s+/', '', $content);
        $content = preg_replace("/\t/","",$content); //使用正则表达式替换内容，如：空格，换行，并将替换为空。
        $content = preg_replace("/\r\n/","",$content);
        $content = preg_replace("/\r/","",$content);
        $content = preg_replace("/\n/","",$content);
        $content = preg_replace("/  /","",$content);
        print_r(json_encode($content));
        exit;
        $rst = ClientUtil::getBookUserForUnionId('oNirq0FTxP1WARQmz2pzJq2nYbkc');
        print_r($rst);
        exit;
        $params = [
            'app_id' => 14,
        ];
        if (!JobUtil::AddCustomJob('getUserBeanstalk', 'get_user', $params, $error, (60 * 60 * 48))) {
            print_r($error);
            exit;
        }
        echo 'OK';

        exit;
        \Yii::$app->cache->delete('carousels_info');
        echo "1";


    }
} 