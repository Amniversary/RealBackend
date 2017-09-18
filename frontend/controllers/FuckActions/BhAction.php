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
use common\models\StatisticsCount;
use frontend\api\version\CreateCarousels;
use frontend\api\version\GetUserInfo;
use frontend\api\version\WxUserLogin;
use frontend\business\ClientUtil;
use frontend\business\DynamicUtil;
use frontend\business\RongCloud\UserUtil;

use frontend\business\WeeklyUtil;
use yii\base\Action;
use yii\db\Query;
use yii\log\Logger;


class BhAction extends Action
{
    public function run()
    {
        echo "<pre>";
        $rst = ClientUtil::getBookUserForUnionId('oNirq0FTxP1WARQmz2pzJq2nYbkc');
print_r($rst);
        exit;
        $params = [
            'app_id' => 14 ,
        ];
        if(!JobUtil::AddCustomJob('getUserBeanstalk', 'get_user', $params, $error, (60 *60 * 48))){
            print_r($error);exit;
        }
        echo 'OK';

        exit;
            \Yii::$app->cache->delete('carousels_info');
            echo "1";
            exit;
        $data = [
            'action_name' => 'user_login',
            'data' => [
                'type' => 5,
                'RealtechLoginSearch' => [
                    'username' => 'ree',
                    'password' => 'reezhou',
                ],
            ],
        ];
            $api = new WxUserLogin();
            if(!$api->execute_action($data, $rs, $error)) {
                    print_r($error);exit;
            }
        print_r($rs);
exit;
        $data['data'] = [
            'username' => 'admin,1',
        ];
        $api = new GetUserInfo();
        if (!$api->execute_action($data, $rstData, $error)) {
            print_r($error);
            exit;
        }
        var_dump($rstData);

        exit;
        $rst = Articles::find()->select(['id', 'title', 'pic', 'description', 'url'])
            ->where(['status' => 1])
            ->offset((1 - 1) * 20)
            ->limit(20)->all();
        print_r($rst);
        exit;
        \Yii::$app->cache->delete('carousels_info');
        exit;
        $rst = WeeklyUtil::GetWeeklyList();
        print_r($rst);
        exit;
        $data['data'] = [
            'pic_url' => 'www.baidu.com',
            'url' => 'www.baidu.com',
            'type' => 1,
        ];
        $api = new CreateCarousels();
        if (!$api->execute_action($data, $rst, $error)) {
            print_r($error);
            exit;
        }

        print_r($rst);
        exit;




        exit;
        set_time_limit(0);
        echo "<pre>";
        try {
            $trans = \Yii::$app->db->beginTransaction();
            $sql = 'select app_id ,SUM(new_user) as num from wc_fans_statistics GROUP BY app_id  ';
            $a = \Yii::$app->db->createCommand($sql)->queryAll();
            foreach ($a as $item) {
                $auth = StatisticsCount::findOne(['app_id' => $item['app_id']]);
                $auth->cumulate_user = $auth->count_user + $item['num'];
                if (!$auth->save()) {
                    print_r($auth->getError());
                    exit;
                }
            }
            echo "ok";
            $trans->commit();

        } catch (Exception $e) {
            $trans->rollBack();
            print_r($e->getMessage());
        }

        exit;
        $mp3 = \Yii::$app->basePath . '/web/tttt/getvoice.mp3';
        $m = new mp3file($mp3);
        $a = $m->get_metadata();
        print_r($a);

        exit;
        $rst = MoneyUtil::ParseNumber(888);
        var_dump($rst);
        exit;
        $config = '';
        $test = new LssClient($config);

        // $test->createSession();

        exit;
        $ac = UsualFunForStringHelper::CreateGUID();
        $sql = 'insert into mb_reward (living_before_id,reward_user_id,living_master_id,gift_id,gift_name,gift_type,gift_value,multiple,total_gift_value,receive_rate,create_time,status,op_unique_no)
values(:lid,:ruid,:luid,:gid,:gname,:gtype,:gvalue,:mul,:total_gift,:rate,:ctime,:tag,:uno)';
        $result = \Yii::$app->db->createCommand($sql, [
            ':lid' => 11,
            ':ruid' => 3,
            ':luid' => 25,
            ':gid' => 1,
            ':gname' => '111',
            ':gtype' => 1,
            ':gvalue' => 10,
            ':mul' => 1,
            ':total_gift' => 10,
            ':rate' => 0,
            ':ctime' => date('Y-m-d H:i:s', time()),
            ':tag' => 1,
            ':uno' => $ac,
        ])->execute();


        //$count = UsualFunForStringHelper::mt_rand_str(10,'1234567890');

        /*$sql = 'insert into mb_test(count,aaa) VALUE (:count,:aa)';
        $rst = \Yii::$app->db->createCommand($sql,[':aa'=>$ac,':count'=>$count])->execute();
        echo $rst;*/
        echo "<br />";
        //$reward_id = \Yii::$app->db->lastInsertID;
        $sql = 'SELECT LAST_INSERT_ID()';
        $reward_id = \Yii::$app->db->createCommand($sql)->queryScalar();
        echo $reward_id;
        echo "<br />";
        //$reward_id = \Yii::$app->db->lastInsertID;
        $sql = 'SELECT LAST_INSERT_ID()';
        $reward_id = \Yii::$app->db->createCommand($sql)->queryScalar();
        echo $reward_id;
        exit;
        $a = ' ';
        var_dump(!isset($a));
        exit;
        $length = 40;
        $rand_str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;

        for ($i = 0; $i < $length; $rand_str .= $strPol[rand(0, $max)], $i++) ;
        echo $rand_str;
        exit;

        $client = ClientUtil::GetClientNo(strval($client_no));
        if (!isset($client)) {
            echo "该用户不存在";
            exit;
        }
        if (empty($client->getui_id)) {
            echo "个推ID为空";
            exit;
        }
        $test = '25-这是一条测试推送';
        $test_od = '这是一条测试推送';
        if (!GeTuiUtil::PushSingleMessage($test_od, $test, $client->getui_id, 'com.mb.show', $error)) {
            echo '蜜播ID: ' . $client->client_no . '  个推ID: ' . $client->getui_id;
            echo "<br />";
            print_r('error :' . $error);
            exit;
        }
        echo "ok";
        echo "<br />";
        echo '蜜播ID: ' . $client->client_no . '  个推ID: ' . $client->getui_id;
        exit;
        // require dirname(\Yii::$app->getBasePath()) . '/common/components/bce-php-sdk-0.8.23/BaiduBce.phar';
        $RongCloud = \Yii::$app->im->User();
        $nick_name = ' ';
        $pic = ' ';
        $rst = $RongCloud->getToken(25, $nick_name, $pic);
        if (!$rst) {
            $error = $RongCloud->getErrorMessage();
            print_r($error);
            exit;
        }
        print_r($rst);
        exit;
        $id = rand();
        echo $id;
        exit;
        $rst = UsualFunForStringHelper::CreateGUID();
        print_r($rst);
        exit;
        $srt = '01';
        $str = UsualFunForStringHelper::mt_rand_str(64, $srt);
        print_r($str);



        exit;
        $inso = [
            'contend' => '',
            'extra' => ['level_no' => 1],
            'type' => 111,
        ];

        echo "ok";

        exit;
        $data = [
            'key_word' => 'send_level_im',
            'userId' => 253639,
            'levelNo' => 1000,
        ];

        if (!JobUtil::AddImJob('tencent_im', $data, $error)) {
            \Yii::error($error);
            exit;
        }
        echo "ok";




        exit;
        $str = UsualFunForStringHelper::mt_rand_str(32);

        print_r($str);




        $real_money = 0.00;
        $real_money = 4580;
        $rate = intval(14.9);
        $ticket_num = sprintf('%0.0f', ($real_money * $rate));
        var_dump($rate);
        echo "<br />";
        var_dump($ticket_num);
        exit;
        $post = [
            'rand_str' => '2P85T5CAZ5UA5A7691FX9F6P3OKFB6T7',
            'time' => '1490279092416',
            'token' => 'c314df4060a593f1798f5994c012d02335acba2a',
            'data' => 'Z3VEPSa/EVakzof2WthFz6I7W4dw6fhspT22S9rab6KtPYs8kF+qZaSXvOc4yPKTrlYsU11qeF8FJFixtGytKvo2wcgS56iw6plrGfBVzz8dA4aPd6LgVpcIHgoFQP5x4wrqL4APh4JI4awmEGZyn9micb9+qz77y1z+cI1X+JdZmrDqSPoEBSqlVSXGVvYB/AroDqgvMZqa/UoPF/LHzRoXcJ5XrlyUC9rmRzEVYVINlAG2EWmR3WEu97WeGgabHbRvItccMPls/jS5s1eXcg==',
            'token_other' => '1d9e60da4cb56c776642683bb75fa603'
        ];
        list($rand_str, $time, $token, $data, $token_other) = $post;
        $sign_key = '9e9581eb2f8c4c002f88405cd1444327';
        $rst = ApiCommon::GetApiSign($rand_str, $time, $token, $data, $sign_key);
        echo "<pre>";
        print_r($rst);
        exit;
        \Yii::$app->cache->set('112', 112313, 60);
        $rs = \Yii::$app->cache->get('112');

        echo $rs;



        exit;
        $userInfo = ClientUtil::GetClientById(285);
        echo "<pre>";
        print_r($userInfo);


        exit;
        $json = '{"messageId":"Notification(endpoint=http://fronttest.mblive.cn/baidu/baidu_notify, innerName=ntf-hcvr5rav743ebf1x, inUse=false)","messageBody":"{\"sessionId\":\"p-hcsqevcgaebs40ccaj\",\"playDomain\":\"rtmp.push5.bd.mblive.cn\",\"app\":\"room\",\"stream\":\"mb\",\"requestId\":\"e51d8588-7b03-420d-a838-88431fdef961\",\"expire\":\"2017-03-20T09:24:19Z\",\"body\":{\"alarmType\":\"SEXY\",\"confidence\":20.781528,\"imageUrl\":\"http://lss-audit-check-bj.bj.bcebos.com/rtmp.push5.bd.mblive.cn/room/mb/thumbnails/20170320165418.jpg?authorization=bce-auth-v1%2F5545106385ba4db7bae23f06e40f3885%2F2017-03-20T08%3A54%3A19Z%2F3600%2Fhost%2F80441d90cba40d62cc06135ae80d3d397ea0869a78ba3f121c2a174c5e7a8db2\"},\"notifyTime\":\"2017-03-20T08:54:19Z\",\"type\":\"IllegalContentDetected\"}","subscriptionName":"ntf1490000059094","version":"v1alpha","signature":"QNS signature"}';
        $rst = json_decode($json, true)['messageBody'];
        $data = json_decode($rst, true);
        echo "<pre>";
        print_r($data);
        exit;
        //
        $living_id = '';
        $json = [
            'key_word' => 'send_danmu',
            'content' => 'sasadasdasd1231321',
            'living_id' => $living_id,
            'user' => [
                'id' => 1,
                'name' => 'dashjcxmj',
                'icon' => 'aaaaaaaaaa',
            ],
            'type' => 205,
            'extra' => ['is_super' => intval(1), 'level_no' => intval(123)]
        ];
        unset($json['key_word']);
        unset($json['living_id']);
        $json = json_encode($json);
        echo $json;
        $rc = \Yii::$app->im->Message();
        if (!$rc->publishChatroom(285, 7558, 'MB:gifts', $json)) {
            echo 'no' . "\n";
            print_r($rc->getErrorMessage());
            exit;
        }
        echo "ok\n";
        print_r($rc->getErrorCode());
        exit;
        exit;


        $rst = StatisticActiveUserUtil::WeekGift();
        print_r($rst);
        exit;
        set_time_limit(0);

        $st = microtime(true);
        echo "<pre>";
        for ($i = 0; $i < 500; $i++) {
            $content = '';
            $json = json_encode($content);
            $rc = \Yii::$app->im->Message();
            if (!$rc->publishChatroom('-1', 7558, 'MB:gifts', $json)) {
                print_r($rc->getErrorMessage());
                echo "<br/>";
                print_r($rc->getErrorCode());
                exit;
            }
        }
        $en = microtime(true);
        $rst = $en - $st;
        echo $i;
        echo "<br />";
        echo $rst;



        exit;
        $all_living = LivingUtil::GetAllLivingMasterGroupTest();   //得到所有正在直播的用户
        echo "<pre>";
        print_r($all_living);
        exit;
        $data[]['l'] = 1;
        $data[]['l'] = 2;
        unset($data[0]);
        print_r($data);
        exit;
        $int = 1.00;
        $bit = 0.01;
        $max = 0;
        for ($i = 0; $i <= 10000; $i++) {
            $max += $bit;
            if (floatval($max) == floatval($int)) {
                break;
            }
            sleep(1);
        }
        print_r($max . ' ');
        print_r($i);


        exit;
        \Yii::$app->cache->delete('luckgift_list_info');
        exit;
        $cnt = \Yii::$app->cache->get('luckgift_list_info');
        print_r($cnt);
        exit;
        $luckChance[] = [
            'lucky_id' => 1,
            'receive_rate' => 50,
            'basic_beans' => 1,
            'multiple' => 5,
            'rate' => 1,
            'status' => 1,
            'create_time' => '2016-09-29 10:15:36'
        ];
        $giftValue = 20;
        echo "<pre>";
        if (!LuckyGiftUtil::GetPassRateInfoTest($giftValue, $luckChance, $out, $error)) {
            print_r($error);
            exit;
        }
        print_r($out);
        array_pop($out['rate_list']);
        print_r($out);
        exit;
        $outInfo['rate_list'] = [35, 65];
        $outInfo['float_len'] = 0;
        $rate_list_key = LuckyGiftUtil::GetRandRate($outInfo['rate_list'], $outInfo['float_len']);
        echo "<pre>";
        print_r($rate_list_key);
        exit;

        //TODO: 腾讯幸运礼物im
        $im_data = [
            'key_word' => 'send_lucky_gift_im',
            'user_id' => 285,
            'nick_name' => 'Gavean  »',
            'level_no' => 63,
            'pic' => 'http://q.qlogo.cn/qqapp/1105405817/323FDBA21D47FD9AA4C8E86C84AF1876/100',
            'multiple' => '5',
            'total_beans' => 50,
            'other_id' => '@TGS#3TTY7IGEO'
        ];
        $json = json_encode($im_data);
        echo "<pre>";
        if (!JobUtil::AddCustomJob('ImBeanstalk', 'tencent_im', $im_data, $error)) {
            \Yii::getLogger()->log('幸运礼物im消息发送失败：' . 'fail:' . $error . ' date_time:' . date('Y-m-d H:i:s'), Logger::LEVEL_ERROR);
            echo 'NO';
            exit;
        }
        echo "ok";
        exit;
        $a = [
            'content' => '',
            'user' => [
                'id' => 1,
                'name' => '昵称',
                'pic' => '头像',
            ],
            'type' => 203,
            'extra' => [
                'level_no' => '等级',
                'multiple' => '倍数',
                'total_beans' => '总豆数',
            ],
        ];
        echo '<pre>';
        $rst = json_encode($a);
        echo $rst;
        print_r($a);
        exit;
        $a = \Yii::$app->request->get('a');
        $stat = date("Y-m-d H:i:s");
        echo $stat . "\n";
        $lock = new PhpLock('ok_key');
        $lock->lock();
        if ($a) {
            $lock->unlock();
        }

        echo "OK\n";
        sleep(10);
        echo date('Y-m-d H:i:s'), "\n";
        $lock->unlock();
        echo "un-lock\n";
        exit;
        $a = GiftUtil::GetGiftById(1);


        exit;
        $living_info = LivingUtil::GetSendGiftLivingInfo(7558);
        echo "<pre>";
        print_r($living_info);

        exit;

        $userInfo = ClientUtil::GetClientById(285);
        $params = [
            'key_word' => 'send_danmu',
            'content' => '果果sb',// $data['text'],
            'living_id' => 7558,
            'user' => [
                'id' => $userInfo['client_id'],
                'name' => $userInfo['nick_name'],
                'pic' => $userInfo['pic'],
            ],
            'extra' => [
                'type' => 210,
            ]
        ];
        if (!JobUtil::AddCustomJob('ImBeanstalk', 'tencent_im', $params, $error)) {
            \Yii::error($error . ' im danmu队列job异常');
        }
        echo 'ok';
        exit;

        $in = 'INSERT ignore INTO mb_activity_girl(type,user_id) VALUE(0,1);';
        \Yii::$app->db->createCommand($in)->execute();
        $sql = 'SELECT LAST_INSERT_ID()';
        $group_id = current(\Yii::$app->db->createCommand($sql)->queryOne());
        echo "<pre>";
        print_r($group_id);

        exit;
        $uniqueNO = '15857108643';
        $user = ClientUtil::GetUserByUniqueId($uniqueNO);
        echo "<pre>";
        print_r($user['client_id']);
        exit;
        $rst = LivingUtil::GetLivingAndUserInfoByUniqueId('323FDBA21D47FD9AA4C8E86C84AF1876');
        echo "<pre>";
        print_r($rst);
        exit;
        $rst = ActivityStatisticUtil::ActivityGirlInfo();
        echo "<pre>";
        print_r($rst);
        exit;
        $now = time();
        $getValue = (new Query())
            ->select(['living_master_id', 'sum(gift_value) as gift_value'])
            ->from('mb_reward')
            ->where('gift_type = 1 and status = 1 and create_time between :stat and :end', [
                ':stat' => date('Y-m-d H:i:s', 1488943202),
                ':end' => date('Y-m-d H:i:s', $now)])
            ->groupBy('living_master_id')
            ->orderBy(['gift_value' => SORT_DESC])->all();
        echo "<pre>";
        print_r($getValue);
        exit;
        $rst = ActivityStatisticUtil::GirlCache();
        echo "<pre>";
        $rst = json_decode($rst, true);
        print_r($rst);
        exit;
        $ups = '';
        $ups .= sprintf('update mb_activity_girl set value = value + %d WHERE user_id = %d AND type = 1', 1, 1);
        echo $ups;
        exit;
        $ins = 'insert ignore into mb_activity_gril(user_id,value,type) VALUES';
        $ins .= sprintf('(%d,%d,1)', 1, 1);
        echo $ins;
        exit;


        $base['get_count'] = 0;
        $now = time();
        $rewardValue = (new Query())
            ->select(['reward_user_id', 'sum(gift_value) as gift_value'])
            ->from('mb_reward')
            ->where('gift_type = 1 and status = 1 and create_time between :stat and :end', [
                ':stat' => date('Y-m-d H:i:s', $base['get_count']),
                ':end' => date('Y-m-d H:i:s', $now)])
            ->groupBy('reward_user_id')
            ->orderBy(['gift_value' => SORT_DESC])->all();
        echo '<pre>';
        print_r($rewardValue);
        exit;
        $test = '{"content":"这是一条系统消息!"}';
        $system = \Yii::$app->im->Message();
        if (!$system->broadcast('-1', $test)) {
            print_r($system->getErrorMessage());
            exit;
        }
        echo "ok";
        exit;
        $sta = new \stdClass();
        $sta->user_id = [
            'userID' => '1',
        ];
        $sta->aaa = 2;
        echo "<pre>";

        print_r($sta->user_id['userID']);


        exit;
        $device_no = '23eye627y342s34';
        $rst = Client::find()->select(['count(*) as num'])->addGroupBy('device_no')->limit(1)->where(['device_no' => $device_no])->scalar();
        exit;
        $a = Client::findOne(['unique_no' => '15857108643']);
        echo "<pre>";
        print_r($a);

        $a['client_id'] = 3;
        $a->save();
        $a = Client::findOne(['unique_no' => '15857108643']);
        print_r($a);
        exit;

        //验证码GD库
        $image = imagecreatetruecolor(100, 30) or die ('Cannot create image Resources');  // 创建一张资源图片
        $bg_color = imagecolorallocate($image, 255, 255, 255);  // 设置图片底色  为纯白
        imagefill($image, 0, 0, $bg_color);  // 将背景色填充

        $font_size = 6; // 定义字体大小
        for ($i = 0; $i < 4; $i++) {
            $font_color = imagecolorallocate($image, rand(0, 100), rand(0, 100), rand(0, 100)); // 设置填充的字体颜色
            $data = 'AaBbCcDdEeFfGgHhIiJjKkLMmNnOoPpQqRrSsTtUuVvWwXxYyZz1234567890';
            $font_contend = substr($data, rand(0, strlen($data) - 1), 1);  // 每次随机的数字

            $x = ($i * 100 / 4) + rand(5, 10);  //数字的x轴定位
            $y = rand(5, 10);  // 数字的y轴定位

            imagestring($image, $font_size, $x, $y, $font_contend, $font_color);  // 数字填充到图片资源
        }

        for ($i = 0; $i < 200; $i++) {
            $int_color = imagecolorallocate($image, rand(50, 200), rand(50, 200), rand(50, 200));  //
            imagesetpixel($image, rand(1, 99), rand(1, 29), $int_color);
        }

        for ($i = 0; $i < 2; $i++) {
            $line_color = imagecolorallocate($image, rand(80, 220), rand(80, 220), rand(80, 220));
            imageline($image, rand(1, 99), rand(1, 29), rand(1, 99), rand(1, 29), $line_color);
        }

        header('content-type: image/png');

        imagepng($image);
        imagedestroy($image);
        exit;

        exit;
        $text_content = '推推推图推推推图头头ITUITUITUiu';
        if (!TimRestApi::openim_batch_send_msg($text_content, $error)) {
            print_r('发送推送消息失败:' . $error);
            exit;
        }

        echo 'OK';
        exit;

        //测试聊天之消息推送 融云
        $message = \Yii::$app->im->Message();
        if (!$message->publishChatroom('-1', '13050', 'MB:clicklike', '{"content":"c#hello","extra":{"level":"20"}}')) {
            print_r($message->getErrorMessage());
            exit;
        }

        echo 'ok';




        exit;
        $str = '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';
        $compressed = gzcompress($str, 9);//压缩级别为9
        $uncompressed = gzuncompress($compressed);
        echo $compressed, "\n";
        echo $uncompressed, "\n";
        echo strlen($compressed), "\n";
        exit;
        echo "<pre>";
        $data['userId'] = '285';
        $data['name'] = 'Gavean  »';
        $data['pic'] = 'http://q.qlogo.cn/qqapp/1105405817/323FDBA21D47FD9AA4C8E86C84AF1876/100';
        if (!UserUtil::refreshUserInfo($data, $error)) {
            print_r($error);
            exit;
        }
        echo 'OK';
        exit;
        if (!UserUtil::getUserToken('285', $rstData, $error)) {
            print_r($error);
            exit;
        }

        print_r($rstData);
        exit;
        $data = '{"user_id":740282,"nick_name":"\ud83d\udd05A\u52ff\u5fd8\u521d\u5fc3\ud83d\udd06","pic":"http:\/\/mbpic.mblive.cb\/client-pic\/ocimg_585d8b81c2a91.jpg","content":"","dynamic_id":48,"dynamic_pic":"http:\/\/mbpic.mblive.cn\/user\/fef8683df845fbf434d5229b1baaefdc.jpg","create_time":"2017-02-28 12:21:35","type":"1","reward_money":1}';
        echo "<pre>";
        print_r(json_decode($data));
        exit;
        $rst = AlipayUtil::QueryOrderStatus('ZHF-RG-17-02-214737', '', $error);
        print_r($rst);

        EXIT;
        \Yii::$app->cache->set('111', 'aaaa');
         $rst = \Yii::$app->cache->get('111');
        print_r($rst);



        exit;
        var_dump('mysqli:' . extension_loaded('mysqli'));
        var_dump('curl:' . extension_loaded('curl'));
        echo "<hr />";
        var_dump('bz2:' . extension_loaded('bz2'));
        var_dump('PDO:' . extension_loaded('PDO'));
        echo "<hr />";
        var_dump('GD2:' . extension_loaded('GD2'));
        var_dump('memcache:' . extension_loaded('memcached'));
        echo "<hr />";
        print_r("<pre>");
        print_r(get_loaded_extensions());
        exit;
        $time = $_SERVER['REQUEST_TIME'];
        echo $time;
        exit;
        $page_no = 1;
        $page_size = 10;
        $offset = $page_size * ($page_no - 1);
        $key_word = '_韩国';
        $str = ['%' => '\%', '_' => '\_', '\\' => '\\\\'];
        $key_word = strtr($key_word, $str);
        echo $key_word;
        exit;
        $query = new Query();
        $query->select(['client_id as user_id', 'ct.nick_name', 'ifnull(ct.icon_pic,ct.pic) as pic', 'sex', 'sign_name', 'ifnull(c.friend_user_id,0) as is_attention'])
            ->from('mb_client ct')
            ->leftJoin('mb_attention c', 'c.friend_user_id = ct.client_id and user_id=:uid', [':uid' => strval(intval(1))])
            ->where(['or', ['like', 'client_no', $key_word], ['like', 'ct.nick_name', $key_word]])
            ->offset($offset)
            ->orderBy('ct.client_id asc')
            ->limit($page_size);
        print_r("<pre>");
        print_r($query->createCommand()->rawSql);

        exit;

        //AES 解密
        $data = '4YGftXVa9emBjPi91RnSw+RWbGc5lstexjB3iaVk19ea7pVYmMxhuT8fGNYc0x2Jj8UOtsTYF0va+xeTyz9YWxHcB7aeYMntEx8z5Qo454paVbABHtU+S3X0ui8ejECCRmxluD8nx0BaL1K6P9xOgHSqL5dJtdE92zVk7BhU1vR8CnntN4yLxVkpQ3mVPyHlPKQ1C3J0YAwA1bRzzKNrVFEyM19TluzVnvxspgKxUmABJwPQG5R3T5VKfjATqh/qErdZebGFu018MIez/DB+yoBnKfR7vRmmlB5VWGb/JAU=';
        $asc = new AESCrypt('05f3176e0d0e6772d5b6a78b939889d6');
        $rst = $asc->decrypt($data);
        print_r("<pre>");
        print_r($rst);
        exit;
        //AES 加密
        $data = '{"app_id":"1171658345","action_name":"update_key","app_version_inner":"1","has_data":"1","data":{},"device_no":"FC63BD1D-252E-48D2-8BF6-3EC40A5816CD","api_version":"v3","device_type":"2","data_type":"string"}';
        $asc = new AESCrypt('05f3176e0d0e6772d5b6a78b939889d6');
        $rst = $asc->encrypt($data);
        print_r("<pre>");
        print_r($rst);

        exit;


        phpinfo();
        exit;
        $value = time();
        //在这里设置一个名为test的Cookie
        setcookie('test', $value);
        if (isset($_COOKIE['test'])) {
            echo 'success';
            echo "<br/>";
            echo $_COOKIE['test'];
        }
        exit;
        $rst = \Yii::$app->cache->get('set_admin_warning_15203');
        $data = unserialize($rst);
        var_dump($data);
        exit;

        exit;
        $aa = ClientUtil::GetClientById(42344);
        $user_id = 42344;
        $name = $aa->nick_name;
        $sql = 'select nick_name from mb_client where nick_name = :name';
        $rst = \Yii::$app->db->createCommand($sql, [':name' => $name])->queryAll();
        print_r($name);
        print_r('<br />');
        print_r($rst);
        exit;
        //测试大汉三通短信
//$a  ='{"account":"dh17481","password":"7d0ed9995eea07c2c913b9f5e1954f1f","msgid":"C1DE15A9-EDDD-4551-19E2-7D9876C754C3","phones":"15857108643","content":"\u6d4b\u8bd5\u77ed\u4fe1","sign":"","subcode":"17481","sendtime":""}';
        $data = [
            'account' => 'dh17481',
            'password' => md5('TS60pZq6'),
            'msgid' => UsualFunForStringHelper::CreateGUID(),
            'phones' => '15857108643',
            'content' => '测试短信',
            'sign' => '【学信宝】',
            'subcode' => '17481',
            'sendtime' => '',
        ];
        $json_data = json_encode($data);
        $url = 'http://www.dh3t.com/json/sms/Submit';
        $rst = UsualFunForNetWorkHelper::HttpsPost($url, $json_data);

        print_r("<pre>");
        print_r($rst);
        print_r("<br >");
        print_r($json_data);

        exit;


        $unique_no = 'B79A732D2E896226B1B5F125B4CD69BE';
        $a = ClientLivingParamtersUtil::QueryClientLivingParamtersByUniqueNo($unique_no);
        print_r('<pre>');
        print_r($a);
        EXIT;
        $app_id = '1172645364';
        $current_time = date('Y-m-d H:i:s');
        $adList = Advertise::find()
            ->asArray()
            ->select(['app_id', 'img_url', 'width', 'height', 'duration'])
            ->andFilterWhere(['app_id' => $app_id, 'status' => 1])
            ->andFilterWhere(['<=', 'effe_time', $current_time])
            ->andFilterWhere(['>=', 'end_time', $current_time])
            ->orderBy('ordering DESC')
            ->one();
        print_r("<pre>");
        print_r($adList);

        exit;
        $maxNum = SystemParamsUtil::GetSystemParam('system_device_register_no', true);
        print_r($maxNum);
        $device_no = '862844035254307';
        $num = ClientUtil::GetDeviceRegisterNum($device_no);
        echo "<br />";
        print_r($num);
        if ($num > $maxNum) {
            $error = '同一设备最多注册四个账号';
            echo $error;//\Yii::getLogger()->log($error.'  max_num:'.$maxNum.'  num:'.$num.'  device_no:'.$device_no,Logger::LEVEL_ERROR);
            return false;
        }
        exit;
        $iosbuygoods = require(\Yii::$app->getBasePath() . '/../common/config/IosBuyGoodsList.php'); //苹果内购商品名称及价格对应信息
        print_r($iosbuygoods['com.my.MiBo100']);
        exit;
        $receipt_data = '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';
        $receipt_data = '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';
        $receipt_data = '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';
        $receipt_data = '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';
        $data = IOSBuyUtil::GetIosBuyVerify($receipt_data, false);
        print_r("<pre>");
        print_r($data);
        exit;
        echo "Hi " . "there. " . "how are " . "you?";
        echo "<br />";
        echo "Hi ", "there. ", "how are ", "you?";
        echo "<br />";
        $name = 'zcxx';
        echo "hi $name";
        echo "<br />";
        echo 'hi ', $name;
        exit;
        $data = date('Y-m-d', strtotime('-30 day'));
        $rst = date('Y-m-d');
        $aaa = strtotime($rst) - strtotime($data);
        print_r($data);
        print_r("<br>");
        print_r($aaa);

        exit;

        $rst = UpdateContentUtil::GetUpdateVersion();
        print_r("<pre>");
        print_r($rst);

        exit;
        $RST = \Yii::$app->cache->get('update_version');
        $rst = json_decode($RST, true);
        print_r("<pre>");
        print_r($rst);



        exit;
        $star = microtime(true);
//        $subQuery = Client::find()->where('client_id > 0');
//        $count = $subQuery->count('1');
        $query = (new Query())
            ->select(['client_id', 'client_no', 'nick_name', 'bean_balance', 'virtual_bean_balance', 'ticket_count', 'ticket_real_sum', 'ticket_count_sum', 'virtual_ticket_count', 'send_ticket_count', 'freeze_status'])
            ->from('mb_client bc')
            ->innerJoin('mb_balance bb', 'bc.client_id = bb.user_id')
            ->where('bc.client_id > 0');
        $count = $query->count('bc.client_id');
        print_r($count);
        $end = microtime(true);
        $rst = $end - $star;
        echo "<br />";
        echo $end;
        echo "<br />";
        echo $star;
        echo "<br />";
        print_r($rst);
        /*print_r("<br />");
        print_r("<pre>");
        $pagination = new Pagination(['totalCount' => $count]);
        print_r($pagination);

        $subQuery->offset($pagination->offset)
            ->limit($pagination->limit);

        $query = ClientNoList::find()->select('*')->from(['t1' => ClientNoList::tableName(), 't2' => $subQuery])
            ->where('t1.record_id=t2.record_id');
        $articles = $query->all();

        print_r($articles);*/
        exit;
        $a = ClientInfoUtil::GetAdminWarningByUser(1);
        print_r("<pre>");
        print_r($a);

        exit;
        $rst = \Yii::$app->cache->get('mb_api_login_16666666666');
        print_r($rst);


        exit;


        //给某人推送  个推
        //$cid = 'eebb4fda0f24ab725e66537ca7086617';  //我
        //$cid = '2697ec222c253d6ed4706fb9ad951266'; //焦亚
        //$cid= '1354593024dfc58221226d3f6bf335e6'; //书院
        $cid = 'b63901de91186b6a2e88643a77a93e20';
        $cid = '05b91397269ab2d999bce22ac6d2e7c6';
        $content = '25-前方有一大波主播正在赶来中~~颜值也不错哦~！听说不同的人对这句话有着不同的理解';
        $show_content = '前方有一大波主播正在赶来中~~颜值也不错哦~！听说不同的人对这句话有着不同的理解，禾禾禾~~~你是哪种捏？抓紧撩吧，小蜜只能帮你到这儿啦！';
        if (!GeTuiUtil::PushSingleMessage($show_content, $content, $cid, '1171658345', $error)) {
            print_r($error);
            exit;
        }
        print_r($error);
        print_r('ok');

        exit;
        $api_versions = ApiCommon::GetApiVersion();
        print_r($api_versions);
        exit;

        return $this->controller->render('testbh');

        exit;
        $sql = 'select * from mb_client_no_list ORDER BY client_no limit 500000';
        $rst = \Yii::$app->sphinx->createCommand($sql)->queryAll();
        //$rst = \Yii::$app->db->createCommand($sql)->queryAll();
        print_r("<pre>");
        print_r($rst);

        exit;
        $name = 'Gevean';
        if (!ClientUtil::CheckNameIsNull($name, $error)) {
            print_r($error);
            exit;
        }

        print_r('ok');


        exit;
        $group_id = '@TGS#3ND6ZHKE2';
        if (!TimRestApi::group_destroy_group($group_id, $error)) {
            print_r($error);
            exit;
        }
        print_r($group_id);
        print_r("<br />");
        print_r('OK');

        exit;
        $rst = LivingUtil::GetLivingTicketGuess('183977', '211883');
        if (!isset($rst)) {
            echo "no";
            exit;
        }
        print_r('<pre>');
        print_r($rst);
        exit;

        $ac = null;
        if (!isset($ac)) {
            echo 'no';
            exit;
        }
        echo 'ok';

        exit;

        if (!TimRestApi::group_forbid_send_msg('@TGS#373P52JEO', strval(696508), 3600 * 5, $error)) {
            echo "no";
            return false;
        }
        echo 'OK';


        exit;
        $a = 128306 % 5;
        print_r($a);

        exit;
        $a = '
        /etc/vsftpd/vsftpd.conf
        listen=YES
        userlist_enable=NO
        pam_service_name=vsftpd
        tcp_wrappers=YES
        guest_enable=YES
        guest_username=vftp
        virtual_use_local_privs=YES
        user_config_dir=/etc/vsftpd/userconfig
        anonymous_enable=NO
        local_enable=YES
        write_enable=YES
        anon_upload_enable=NO
        anon_mkdir_write_enable=NO
        dirmessage_enable=YES
        use_localtime=YES
        xferlog_enable=YES
        connect_from_port_20=YES
        chown_uploads=NO
        xferlog_file=/var/log/vsftpd.log
        xferlog_std_format=YES
        nopriv_user=vftp
        async_abor_enable=YES
        ascii_upload_enable=YES
        ascii_download_enable=YES
        ftpd_banner=This Vsftp server support virtual users ^_^!
        chroot_local_user=YES
        chroot_list_enable=NO
        ';




        exit;
        $a = LivingUtil::GetLivingAndUserInfoByUniqueId(1);

exit;
        $condition = '1,2,3,4,5,6';
        $query = (new Query())
            ->select(['client_i'])
            ->from('mb_client')
            ->where('client_id in (' . $condition . ')')
            ->all();
        print_r("<pre>");
        print_r($query);


        exit;



        $sql = 'select client_id from mb_client limit 10';
        $rst = \Yii::$app->db->slave->createCommand($sql)->queryAll();
        //\Yii::$app->db->close();

        print_r("<pre>");
        print_r($rst);
        exit;
        $a = Menu::tableName();
        print_r("<pre>");
        print_r($a);



        exit;

        //$sql = 'select * from my_menu limit 1';

        $a = GiftUtil::GetGiftById(2);
        print_r($a);

        exit;

        print_r('a');
        exit;


         $a = ApiCommon::GetApiVersion();
        print_r("<pre>");
        print_r($a);


        exit;
        \Yii::$app->cache->set('aaa', 'ddd');
        $a = \Yii::$app->cache->get('aaa');
        print_r($a);

        exit;
        phpinfo();
        $data = date('Y-m-d H:i:s', strtotime('-30 min'));
        $rst = date('Y-m-d H:i:s', strtotime('-10 min'));
        print_r($data);
        print_r('<br />');

        print_r($rst);
        exit;


        $a = ClientUtil::SearchUser(1, 1, 1, 1);
        print_r($a);

        exit;

        $a = Gift::findOne(['gift_id' => 1]);
        print_r("<pre>");
        print_r($a->attributes);
        exit;
        $a = '218dasdasdsa111111eswadasdas';
        print_r($a);
        exit;
        $array = ['无可奈何花落去', '城标', '辅导就是拉', '弩'];
        asort($array);

        print_r('<pre>');
        var_dump($array);



        exit;
        $a = LivingUtil::GetLivingTicketGuess(1, 3);
        exit;

        $n = 0.1265489;
        $a = 110.97902097902097902097902097902;
        echo strstr($a, 1);
        echo sprintf("%.1f", round($a, 2));

        exit;
        $rst = [1, 2, 3, 4, 5, 6];
        $st = implode(',', $rst);
        print_r($st);
        exit;
        $a = ['1'];
        $info = implode(',', $a);
        var_dump($info);
        exit;
        $card = '330327199601160952';
        $rst = UsualFunForStringHelper::is_identity_card($card);
        var_dump($rst);


        exit;
        //大汉三通短信测试
        $templateid = '1';
        $array_param['param1'] = '8888';
        $tel = '15857108643';
        if (!SendShortMessage::SendMessageDaHanSanTong($tel, $templateid, $array_param, $error)) {
            print_r($error);
            exit;
        }
        print_r('ok');
        exit;
        exit;
        $rst = '{"msgid":"C689D403-5D27-5370-4808-2FF04A65A98E","result":"0","desc":"提交成功","failPhones":""}';
        $rst = json_decode($rst, true);
        print_r("<pre>");
        var_dump($rst);

        exit;


        $data = '{"device_no":"863411030999440","device_type":"1","api_version":"v2","app_version_inner":"28","data_type":"string","has_data":"1","data":{"validate_code":"","unique_no":"B79A732D2E896226B1B5F125B4CD69BE","getui_id":"0093b1f62d97d4230e3a896a5d913758","other_unique_no":"","sex":"男","pic":"http:\/\/q.qlogo.cn\/qqapp\/1105405817\/B79A732D2E896226B1B5F125B4CD69BE\/100","register_type":"4","nick_name":"VictorChen"},"app_id":"com.mb.mibo","action_name":"qiniu_login"}';
        $asc = new AESCrypt('L@v~kJP_Bo(Sae7YjP2(a92z&1OrH^qd');
        $rst = $asc->encrypt($data);
        print_r("<pre>");
        print_r($data);
        print_r("<br >");
        print_r($rst);
        exit;



        exit;
        $a = LivingUtil::GetLivingTicketGuess(11576, 479);
        print_r("<pre>");
        print_r($a);
        exit;
        $a = GetuiVersionUtil::GetGetuiVersions('1119990982', $error);
        print_r($a::APPKEY);


        exit;


        $a = '0';
        if (!isset($a)) {
            echo 'no';
            exit;
        }
        echo '1';

        exit;
        $unique_no = '15857108643';
        $a = \Yii::$app->cache->get('mb_api_login_' . $unique_no);
        $a = unserialize($a);
        print_r($a);

        exit;




        //给整个app 推送消息  个推
        $show_content = '快点来哦';
        $content = '前方有一大波主播正在赶来中~~颜值也不错哦~！听说不同的人对这句话有着不同的理解，禾禾禾~~~你是哪种捏？抓紧撩吧，小蜜只能帮你到这儿啦！';
        if (!GeTuiUtil::PushAppMessage($show_content, $content, $error)) {
            print_r($error);
            exit;
        }
        print_r($error);
        print_r('ok');

        exit;




        $i = 1;
        $a = intval($i . '000001');
        print_r($a);
        exit;
        set_time_limit(0);
        $sql = 'select record_id,room_no from mb_room_no_list where record_id BETWEEN 1 AND 100000 ORDER by record_id';
        $users = \Yii::$app->db->createCommand($sql)->queryAll();
        //$a = GameRebotsHelper::GetJobDates('roomNoBeanstalk','room_no');
        print_r("<pre>");
        print_r($users);


        exit;


        $no = 0.75;
        $num = intval(ceil($no));
        var_dump($num);
        exit;
        $data['device_type'] = '1';
        $data['data'] = [
            'living_id' => '8450',
            'unique_no' => '15397363330',
            'living_type' => '2',
            'guess_type' => '2'
        ];
        $a = new ZhiBoGetGuess();
        if (!$a->excute_action($data, $rstData, $error)) {
            print_r($error);
            exit;
        }
        print_r('<pre>');
        print_r($rstData);
        exit;
        $a = LivingUtil::GetLivingTicketGuess(6205);
        print_r($a);


        exit;

        $a = LivingUtil::GetLivingTicketGuess(6205);
        print_r($a);


        exit;
        $key_word = '要和';
        $page_no = 1;
        $page_size = 1;
        $user_id = 479;
        $fansGroupList = ClientUtil::FansGroupSearch($key_word, $page_no, $page_size, $user_id);
        print_r("<pre>");
        print_r($fansGroupList);
        exit;
        $data['device_type'] = '1';
        $data['data'] = [
            'living_id' => '11134',
            'unique_no' => '15397363330',
            'living_type' => '1',
            'guess_type' => '1'
        ];
        $a = new ZhiBoGetGuess();
        if (!$a->excute_action($data, $rstData, $error)) {
            print_r($error);
            exit;
        }
        print_r('<pre>');
        print_r($rstData);
        exit;

        $out = '0,0,0,0.54,0.035,0,5';
        $a = explode(',', $out);
        sort($a);
        print_r("<pre>");
        print_r($a);
        exit;
        $in = array_count_values($a)[0];
        $b = $in[0];
        print_r("<pre>");
        print_r($in);

        exit;
        $a = \Yii::$app->cache->get('mb_api_login_15397363330');
        $a = unserialize($a);
        print_r("<pre>");
        var_dump($a);
        exit;
        $guess_num = 10;
        $str = SystemParamsUtil::GetSystemParam('guess_ticket_money', true);
        $str_data = explode(',', $str);
        $cash = $str_data[$guess_num - 1];
        $guess_money = intval($guess_num * $cash);
        print_r("<br >");
        print_r($guess_money);
        exit;
        $insql = 'insert ignore into mb_guess_record(living_id,room_no,user_id,guess_num,create_time) VALUE';
        $insql .= sprintf('(%d,%s,%d,%d,\'%s\')',
            1,
            2,
            3,
            4,
            date('Y-m-d H:i:s')
        );
        \Yii::$app->db->createCommand($insql)->execute();
        exit;
        $guess_data = LivingGuessUtil::GetGuessRecord(2, 1);
        //$guess_data->guess_num = 6;


        if ($guess_data->guess_num >= 6) {
            print_r('k');
            exit;
        }
print_r('kaa');
        exit;
        $a = \Yii::$app->cache->delete('mb_api_login_15858279369');
        var_dump($a);

        exit;

        $rst = '';
        if (JobUtil::GetCustomJob('beanstalk', 'room_no', $rebot, $error) === false) {
            return $rst;
        } else {
            $rst = json_decode(json_encode($rebot), true);
        }

        print_r($rst);

        exit;
        $out = [
            'room_no' => '20000001',
        ];
        if (!JobUtil::AddCustomJob('beanstalk', 'room_no', $out, $error)) {
            print_r($error);
            exit;
        }
        print_r('ok');


        exit;



        //测试个人信息获取接口
        $data['data'] = [
            'user_id' => '8',
            'fields' => [
            ],
        ];

        $fields = [
            'user_id' => 'client_id as user_id',
            'nick_name' => 'nick_name',
            'client_no' => 'client_no',
            'alipay_no' => 'alipay_no',
            'pic' => 'IFNULL(nullif(main_pic,\'\'),bc.pic) as pic',
            'level_id' => 'level_id',
            'level_pic' => 'ls.level_pic',
            'sex' => 'sex',
            'color' => 'color',
            'level_bg' => 'level_bg',
            'font_size' => 'font_size',
            'age' => 'age',
            'city' => 'bc.city',
            'sign_name' => 'sign_name',
            'send_ticket_count' => 'send_ticket_count',
            'attention_num' => 'attention_num',
            'funs_num' => 'funs_num',
            'ticket_count_sum' => 'ticket_count_sum',
            'ticket_count' => 'ticket_count',
            'ticket_real_sum' => 'ticket_real_sum',
            'today_ticket_num' => 'IFNULL(real_ticket_num,0) as today_ticket_num',
            'bean_balance' => 'bean_balance',
            'virtual_bean_balance' => 'virtual_bean_balance',
            'cash_rite' => 'cash_rite',
            'is_bind_weixin' => 'is_bind_weixin',
            'is_bind_alipay' => 'is_bind_alipay',
            'is_contract' => 'is_contract',
            'real_validate' => 'is_centification as real_validate',
            'is_attention' => 'is_attention',
            'is_black' => 'is_black',
            'is_live' => 'IFNULL(ll.status,1) as is_live',
            'first_reward' => 'first_reward',
            'living_id' => 'll.living_id',
            'group_id' => 'IFNULL(group_id, \'\') as group_id',
            'is_join' => 'is_join',
            'tx_group_id' => 'IFNULL(tx_group_id, \'\') as tx_group_id',
            'group_name' => 'IFNULL(group_name, \'\') as group_name',
            'group_pic' => 'IFNULL(fg.pic, \'\') as group_pic',
            'advance_notice' => 'IFNULL(advance_notice, \'\') as advance_notice',
        ];
        $userFinish = 2;
        $filesInput = $data['data']['fields'];
        $files = $filesInput;
        if (!is_array($files)) {
            $files = [];
        }
        if (!is_array($filesInput)) {
            $filesInput = [];
        }
        $joint = [      //额外获取拼接的字段
            'is_attention',
            'is_black',
            'today_ticket_num',
            'cash_rite',
            'first_reward',
            'is_join',
        ];
        $back = [];
        foreach ($joint as $un) {
            if (in_array($un, $filesInput)) {
                $back[] = $un;
            }
        }

        $client_info = [ //必须检测的字段
            'user_id',
            'nick_name',
            'pic',
            'sex',
        ];

        if (!empty($filesInput)) {
            foreach ($client_info as $c) {
                if (!in_array($c, $filesInput)) {
                    $filesInput[] = $c;
                }
            }
        }

        $filedRst = [];
        if (!empty($filesInput))   //获取要查询的字段
        {
            foreach ($filesInput as $field) {
                if (!isset($fields[$field])) {
                    $error = '请求的字段不存在';
                    echo $error;
                    exit;
                }
                $filedRst[] = $fields[$field];
            }
        } else {
            foreach ($fields as $field) {
                $filedRst[] = $field;
            }
            $back = $joint;
        }

        $unset = [          //需要删除的字段
            'is_attention',
            'is_black',
            'first_reward',
            'is_join',
        ];
        foreach ($unset as $t)   //删除不需要的字段
        {
            $set_false = array_search($t, $filedRst);
            if ($set_false !== false) {
                unset($filedRst[$set_false]);
            }
        }
        $to_user = 8;
        if (!empty($data['data']['user_id']))    //如果是查询多个,将id组合
        {
            $to_user = $data['data']['user_id'];
        }
        $self_user_id = 8;
        $client_type = $data['data']['client_type'];
        if (!ClientInfoUtil::GetUserData($filedRst, $to_user, $self_user_id, $back, $userInfo, $client_type, $error)) {
            echo $error;
            exit;
        }
        if (empty($in['nick_name']) || empty($in['pic']) || empty($in['sex'])) {
            $userFinish = 1;
        }
        $userInfo['user_finish'] = $userFinish;
        if (empty($userInfo['first_reward'])) {
            unset($userInfo['first_reward']);
        }
        if (!empty($files)) {
            foreach ($client_info as $c) {
                if (!in_array($c, $files)) {
                    unset($userInfo[$c]);
                }
            }
        }
        //金币帐户信息
        $goldsAccountInfo = GoldsAccountUtil::GetGoldsAccountInfoByUserId($userInfo['user_id']);
        $userInfo['gold_account_info'] = $goldsAccountInfo;

        //积分帐户信息
        $integralAccountInfo = IntegralAccountUtil::GetIntegralAccountInfoByUserId($userInfo['user_id']);
        $userInfo['integral_account_info'] = $integralAccountInfo;


        print_r("<pre>");
        print_r($userInfo);
        exit;
//送礼物   1玫瑰 2香蕉 3啤酒 4污 5么么哒 6樱花雨 7爱马仕包包 8阿斯顿马丁
        //        9飞机 10城堡 11黄瓜 12肥皂 13蛋糕 14抱抱熊 15香水 16别墅 18钻戒
        //        19迈凯伦 20游轮 21糖果 23玉兔  24 月饼
        set_time_limit(0);
        $test = '{"msg":"3","type":"505","userinfo":{"user_id":"1277","nick_name":"\u88d8\u6052\u82e5","client_no":"11649170","alipay_no":null,"pic":"http:\/\/oss-cn-hangzhou.aliyuncs.com\/mblive-demo\/client-pic\/ocimg_58098ee88b420.jpg","icon_pic":"http:\/\/oss-cn-hangzhou.aliyuncs.com\/mblive-demo\/client-pic\/ocimg_58098ee8961eb.jpg","level_id":"2","level_pic":"http:\/\/mbpic.mblive.cn\/meibo-test\/level_stage_1.png","sex":"\u5973","color":"#FFFFFF","level_bg":"#cae05a","font_size":"20","age":null,"city":"","sign_name":"\u6ca1\u6709\u4e2a\u6027\uff0c\u6682\u4e0d\u7b7e\u540d!","send_ticket_count":"93","attention_num":"0","funs_num":"0","ticket_count_sum":"0","ticket_count":"0","ticket_real_sum":"0","today_ticket_num":"0","bean_balance":"999907","virtual_bean_balance":"0","cash_rite":0,"is_bind_weixin":"1","is_bind_alipay":"1","is_contract":"1","real_validate":"1","is_live":"1","living_id":"10787","group_id":"","tx_group_id":"","group_name":"","group_pic":"","private_status":"0","is_attention":0,"is_black":1,"is_join":0,"user_finish":2,"is_super":0,"gold_account_info":{"gold_account_id":"2644","gold_account_total":"50","gold_account_expend":"0","gold_account_balance":"50","account_status":"1"},"integral_account_info":{"integral_account_id":"2642","integral_account_total":"0","integral_account_spend":"0","integral_account_balance":"0","account_status":"1"}},"isAdministrator":"1","other_id":"@TGS#3WLHSIGEU"}';
        $text = '{"msg":"3","type":"505","userinfo":{"user_id":"479","nick_name":"123786","client_no":"14573670","alipay_no":null,"pic":"http:\/\/oss-cn-hangzhou.aliyuncs.com\/mblive-demo\/client-pic\/ocimg_580dcaa524715.jpg","icon_pic":"http:\/\/oss-cn-hangzhou.aliyuncs.com\/mblive-demo\/client-pic\/ocimg_580dcaa531faa.jpg","level_id":"30","level_pic":"http:\/\/mbpic.mblive.cn\/meibo-test\/level_stage_3.png","sex":"男","color":"#FFFFFF","level_bg":"#7dd66b","font_size":"20","age":null,"city":"","sign_name":"没有个性，暂不签名!","send_ticket_count":"73808","attention_num":"6","funs_num":"7","ticket_count_sum":"115642","ticket_count":"10","ticket_real_sum":"10","today_ticket_num":"0","bean_balance":"199993","virtual_bean_balance":"26159","cash_rite":0,"is_bind_weixin":"1","is_bind_alipay":"1","is_contract":"1","real_validate":"2","is_live":"0","living_id":"6205","group_id":"3","tx_group_id":"@TGS#2ZBU3CIEU","group_name":"123786的粉丝群","group_pic":"http:\/\/oss-cn-hangzhou.aliyuncs.com\/mblive-demo\/client-pic\/ocimg_580dcaa531faa.jpg","private_status":"0","first_reward":{"user_id":"1693","pic":"http:\/\/mblive-demo.oss-cn-hangzhou.aliyuncs.com\/user\/b1624370909fc28e2ed2f50e22c60677.jpg"},"is_attention":0,"is_black":1,"is_join":1,"user_finish":2,"is_super":1,"gold_account_info":{"gold_account_id":"1488","gold_account_total":"167523","gold_account_expend":"78300","gold_account_balance":"89223","account_status":"1"},"integral_account_info":{"integral_account_id":"1488","integral_account_total":"10000","integral_account_spend":"2500","integral_account_balance":"7500","account_status":"1"}},"isAdministrator":"1"}';
        //点赞
        $client_like = '{"msg":"0","type":"503","userinfo":{"user_id":"479","nick_name":"123786","client_no":"14573670","alipay_no":null,"pic":"http:\/\/oss-cn-hangzhou.aliyuncs.com\/mblive-demo\/client-pic\/ocimg_580dcaa524715.jpg","icon_pic":"http:\/\/oss-cn-hangzhou.aliyuncs.com\/mblive-demo\/client-pic\/ocimg_580dcaa531faa.jpg","level_id":"30","level_pic":"http:\/\/mbpic.mblive.cn\/meibo-test\/level_stage_3.png","sex":"男","color":"#FFFFFF","level_bg":"#7dd66b","font_size":"20","age":null,"city":"","sign_name":"没有个性，暂不签名!","send_ticket_count":"74140","attention_num":"6","funs_num":"7","ticket_count_sum":"115642","ticket_count":"10","ticket_real_sum":"10","today_ticket_num":"0","bean_balance":"199993","virtual_bean_balance":"25827","cash_rite":0,"is_bind_weixin":"1","is_bind_alipay":"1","is_contract":"1","real_validate":"2","is_live":"0","living_id":"6205","group_id":"3","tx_group_id":"@TGS#2ZBU3CIEU","group_name":"123786的粉丝群","group_pic":"http:\/\/oss-cn-hangzhou.aliyuncs.com\/mblive-demo\/client-pic\/ocimg_580dcaa531faa.jpg","private_status":"0","first_reward":{"user_id":"1693","pic":"http:\/\/mblive-demo.oss-cn-hangzhou.aliyuncs.com\/user\/b1624370909fc28e2ed2f50e22c60677.jpg"},"is_attention":0,"is_black":1,"is_join":1,"user_finish":2,"is_super":1,"gold_account_info":{"gold_account_id":"1488","gold_account_total":"167523","gold_account_expend":"78300","gold_account_balance":"89223","account_status":"1"},"integral_account_info":{"integral_account_id":"1488","integral_account_total":"10000","integral_account_spend":"2500","integral_account_balance":"7500","account_status":"1"}},"isAdministrator":"1"}';
        //弹幕
        $danmu = '{"msg":"大大神大神的","type":"504","userinfo":{"user_id":"479","nick_name":"123786","client_no":"14573670","alipay_no":null,"pic":"http:\/\/oss-cn-hangzhou.aliyuncs.com\/mblive-demo\/client-pic\/ocimg_580dcaa524715.jpg","icon_pic":"http:\/\/oss-cn-hangzhou.aliyuncs.com\/mblive-demo\/client-pic\/ocimg_580dcaa531faa.jpg","level_id":"30","level_pic":"http:\/\/mbpic.mblive.cn\/meibo-test\/level_stage_3.png","sex":"男","color":"#FFFFFF","level_bg":"#7dd66b","font_size":"20","age":null,"city":"","sign_name":"没有个性，暂不签名!","send_ticket_count":"74140","attention_num":"6","funs_num":"7","ticket_count_sum":"115642","ticket_count":"10","ticket_real_sum":"10","today_ticket_num":"0","bean_balance":"199993","virtual_bean_balance":"25827","cash_rite":0,"is_bind_weixin":"1","is_bind_alipay":"1","is_contract":"1","real_validate":"2","is_live":"0","living_id":"6205","group_id":"3","tx_group_id":"@TGS#2ZBU3CIEU","group_name":"123786的粉丝群","group_pic":"http:\/\/oss-cn-hangzhou.aliyuncs.com\/mblive-demo\/client-pic\/ocimg_580dcaa531faa.jpg","private_status":"0","first_reward":{"user_id":"1693","pic":"http:\/\/mblive-demo.oss-cn-hangzhou.aliyuncs.com\/user\/b1624370909fc28e2ed2f50e22c60677.jpg"},"is_attention":0,"is_black":1,"is_join":1,"user_finish":2,"is_super":1,"gold_account_info":{"gold_account_id":"1488","gold_account_total":"167523","gold_account_expend":"78300","gold_account_balance":"89223","account_status":"1"},"integral_account_info":{"integral_account_id":"1488","integral_account_total":"10000","integral_account_spend":"2500","integral_account_balance":"7500","account_status":"1"}},"isAdministrator":"1"}';
        TimRestApi::init($error);
        #构造高级接口所需参数
        $msg_content = array();
        //创建array 所需元素
        $msg_content_elem = array(
            'MsgType' => 'TIMCustomElem',       //文本类型  TIMTextElem  TIMCustomElem
            'MsgContent' => array(
                "Data" => $test,
                "Desc" => 'notification',
                "Ext" => 'url',
                "Sound" => 'dingdong.aiff'
            )
        );
        array_push($msg_content, $msg_content_elem);
        $ret = TimRestApi::group_send_group_msg2('524', '@TGS#3WLHSIGEU', $msg_content);
        if ($ret['ActionStatus'] == 'FAIL') {
            $error = (empty($ret['ErrorInfo']) ? $ret['ErrorCode'] : $ret['ErrorInfo']);
            print_r($error);
            exit;
        }
        print_r($error);
        print_r('ok');

        exit;
        $a = ['user_id' => '1277',
            'type' => '505',
            'msg' => 3,
            'isAdministrator' => '1',
            'userinfo' =>
                array(
                    'user_id' => '1277',
                    'nick_name' => '裘恒若',
                    'client_no' => '11649170',
                    'alipay_no' => NULL,
                    'pic' => 'http://oss-cn-hangzhou.aliyuncs.com/mblive-demo/client-pic/ocimg_58098ee88b420.jpg',
                    'icon_pic' => 'http://oss-cn-hangzhou.aliyuncs.com/mblive-demo/client-pic/ocimg_58098ee8961eb.jpg',
                    'level_id' => '2',
                    'level_pic' => 'http://mbpic.mblive.cn/meibo-test/level_stage_1.png',
                    'sex' => '女',
                    'color' => '#FFFFFF',
                    'level_bg' => '#cae05a',
                    'font_size' => '20',
                    'age' => NULL,
                    'city' => '',
                    'sign_name' => '没有个性，暂不签名!',
                    'send_ticket_count' => '93',
                    'attention_num' => '0',
                    'funs_num' => '0',
                    'ticket_count_sum' => '0',
                    'ticket_count' => '0',
                    'ticket_real_sum' => '0',
                    'today_ticket_num' => '0',
                    'bean_balance' => '999907',
                    'virtual_bean_balance' => '0',
                    'cash_rite' => 0,
                    'is_bind_weixin' => '1',
                    'is_bind_alipay' => '1',
                    'is_contract' => '1',
                    'real_validate' => '1',
                    'is_live' => '1',
                    'living_id' => '10787',
                    'group_id' => '',
                    'tx_group_id' => '',
                    'group_name' => '',
                    'group_pic' => '',
                    'private_status' => '0',
                    'is_attention' => 0,
                    'is_black' => 1,
                    'is_join' => 0,
                    'user_finish' => 2,
                    'is_super' => 0,
                    'gold_account_info' =>
                        array(
                            'gold_account_id' => '2644',
                            'gold_account_total' => '50',
                            'gold_account_expend' => '0',
                            'gold_account_balance' => '50',
                            'account_status' => '1',
                        ),
                    'integral_account_info' =>
                        array(
                            'integral_account_id' => '2642',
                            'integral_account_total' => '0',
                            'integral_account_spend' => '0',
                            'integral_account_balance' => '0',
                            'account_status' => '1',
                        ),
                ),
            'other_id' => '@TGS#3WLHSIGEU'];
        print_r("<pre>");
        $test = json_encode($a);
        print_r($test);

        exit;

        $ca = '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';
        $value = 'MMK+HMdRRhQiWGPmITzCS17oCB1UBhkZi9AqqXCB14ahJSbMrnBXJkNSvBaofVsPBUX7sPYqohtbMEebEN2MLOY5DfiAH8y0hYY5ehZTETxFWczUaEVoX5nd/LHQBeg/UXRqV7yT2+m7oXVdtpnYZiVI9Oqdm0ZNAdtesoSz9hXCiVT4UZ4kV2MsneYoKk7hdN6UUz5ZhjgzTW9hCR8PX0BHpU6C99+8iWaDYACiSZcr8KZN4m3FvnEpbCq+NPOAg0qbMUrxx+JNxCP02LEAcZAAqgku/zU/j7zGaKNjJ7Y=';
        $data = 'nV+5TzO6ZNKKF72cib5kBE+4g1GMLsaquRlH9O7zIunW3sJ4XmM6COxqzVwljwygrtnuHNrkeDNMHe1JwOmpPomeEyr4XQKcBCqhaEJpfVgplZviDMEH4s+156iHMprKITDfkfMprZCQszUQwdOlmmSd3kN0gWoqUPD44fukun8AjYqaembronrRRs7izUWEY+zsaXibbCjWZ8Kjjji7U4VYEvaFSE0u31ImG/iLK6S4wmn7VvqPW/Dwzigjx1PD1ousJfW196RG4Wb94zA0Yu6hKyvmFkMQ7cVBunvBpAAzcKOJ3UIKRwmyQ10NZfzKgQkBTKzUtxsBOBkOORLdKA==';
        $key = new AESCrypt('08f3176ead0e6772b5b6378d93918926');
        $out = $key->decrypt($ca);
        $rst = json_decode($out, true);
        print_r("<pre>");
        print_r($rst);

        exit;

        \Yii::$app->cache->delete('my_api_key_c311df4060a593fe798f5a24c012d02337acbc2a');


        exit;
        $str = \Yii::$app->cache->get('mb_api_login_15857108643');
        $query = new Query();
        $logInfo = $query->select(['client_id as user_id', 'device_no', 'ifnull(vcode,\'\') as verify_code', 'unique_no', 'nick_name', 'client_type'])
            ->from(['mb_client'])
            ->where(['unique_no' => 15857108643])
            ->one();
        print_r($logInfo);

        exit;
        if (!GoldsAccountUtil::GetGoldsAccountModleIsExists(1630)) //开设金币帐户
        {
            // GoldsAccountUtil::CreateAnGoldsAccountForNewRegisUser($user_id);
            echo 'no';
            exit;
        }

        echo 'OK';
        exit;
        if (!JobUtil::AddJob('testbh', '', $error)) {
            print_r($error);
            exit;
        }
        print_r('ok');
        exit;


        $value = 'MMK+HMdRRhQiWGPmITzCS17oCB1UBhkZi9AqqXCB14ahJSbMrnBXJkNSvBaofVsPBUX7sPYqohtbMEebEN2MLOY5DfiAH8y0hYY5ehZTETxFWczUaEVoX5nd/LHQBeg/UXRqV7yT2+m7oXVdtpnYZiVI9Oqdm0ZNAdtesoSz9hXCiVT4UZ4kV2MsneYoKk7hdN6UUz5ZhjgzTW9hCR8PX0BHpU6C99+8iWaDYACiSZcr8KZN4m3FvnEpbCq+NPOAg0qbMUrxx+JNxCP02LEAcZAAqgku/zU/j7zGaKNjJ7Y=';
        $data = [
            "app_id" => "1119990982",
            "action_name" => "update_key",
            "app_version_inner" => "10",
            "has_data" => "1",
            "data" => "",
            "device_no" => "51971028-FC42-4FF1-8D41-FD8DAD544B7A",
            "api_version" => "v2",
            "device_type" => "2",
            "data_type" => "string"
        ];
        //$data = '{"app_id":"1119990982","action_name":"update_key","app_version_inner":"10","has_data":"1","data":"","device_no":"51971028-FC42-4FF1-8D41-FD8DAD544B7A","api_version":"v2","device_type":"2","data_type":"string"}';
        $json = json_encode($data);
        //print_r($json);

        $key = new AESCrypt('05f3176e0d0e6772d5b6a78b939889d6');
        $out = $key->encrypt($json);
        print_r($out);
        exit;







        $data = [
            'device_type' => '1',
            'action_name' => 'gather_error',
        ];
        $data['data'] = 'phone_model[-]a12dsad[-]os_version[-]dsazzz0.1[-]error_after_data[-]dasdqwqewqeqwe[-]encrypt_data[-]完全1饿3的说法第三个[-]encrypt_key[-]cxc1ad456s4d56qw[-]token[-]dsadasdqweqweqw[-]result[-]e12321321fsxvxvxc[-]package_name[-]12167q12131***1[-]error_log[-]dsaczzzzxczzzzzz';

        $a = new ZhiBoGatherError();
        if (!$a->excute_action($data, $rstData, $error)) {
            print_r($error);
            exit;
        }
        print_r("<pre>");
        print_r($rstData);
        exit;

        $a = 'repoer_data[-]' . time() . '[-]device_type[-]dsadsadewqj32132[-]phone_model[-]aaaa[-]action_name[-]gather_error[-]os_version[-]v1[-]error_after_data[-]ds5a4das64w6q4ewq[-]encrypt_data[-]dash2u3h289&*^&*%&^$@[-]encrypt_key[-]flwereopipo13[-]token[-]123asdhajkbncxbnmz[-]result[-]asqqqqqqq[-]package_name[-]com.mibo.cn[-]error_log[-]kkkkllllog';
        $data = explode('[-]', $a);
        $len = count($data);
        $test = [];
        for ($i = 0; $i < $len; $i++) {
            if (($i + 1) % 2 != 0) {
                $test[$data[$i]] = $data[$i + 1];
                print_r($i . ' ');
            }
        }
        print_r("<pre>");
        print_r($test);

        print_r($data);

        exit;
            echo(\yii\helpers\Json::encode(array('opendid' => 'dsadsa', 'unionid' => '12321dsXSA###@&*()')));
            exit;
            $data1 = [1, 4, 51, 3, 'dasdas' => 'sda'];
            $data = 'dasdaswdsadqwewqcx';
             $a = \yii\helpers\Html::encode($data);
             $b = json_encode($data1);
            print_r($a);
            print_r("<br>");
            print_r($b);


            exit;
            $rand_str = '622F5YJIT04VK2610X25Z43YC0B3HAB7';
            $time = '1478633944';
                   //1478656535
            $token = '510f692130b5028cffadb077f8ecf1541872f42a';
            $data = 'EKpPpyGmQv+FEttq8tAslaYZHY4naNfsg+sn3aLLlN8HdBFS2iVZsN1ZHLTR7g0KU1506p96Dp2e
    Cpj8wuFDNvr9gled8w3UT3nwbW4DTm/38rhpI8Mgyy4cuhCeqmcJKSxLU8+KOtrsgCR/17qicGWp
    8y+R+kPEYhxhfxsmXQJKc6stWUJ3jPFw6//lqGF/Yz1dLU7fHBzrj18knXo5yxd1Etl+Gy0tuHcj
    7U1eqnsGOlcUhDikmTz5Y42z1ErUvddzO/qgJxqcK4UwDSYW1++6gyjfjyqkyPz18JePM65mgMU0
    5TlCJEv25u46ZlBx0ESoahuuaYEo4UO/xsBXlmhN0r5Twc/5eWaZ/ep0lmm3oG9RuzhVob2dxNEA
    tBu8du8sQFRKvNx6erk9zni+7Gm251e/sHTa56jfGLjLUb3HNwVqHCOJMg1GUm/yAIeOMg0bQPul
    qrySNKVN40lEzjpp33gqKbwI1Rru/7+au8Wez7YrrQzvsJKj/41YzV28i9p8+5/CKaXoVvxm95oZ
    4JBHxgTkDmlEGnIhsH382yQxWR2QLHflRIhCqlyRK3NTCVqt4yvUGQVEK/mJQri2dVgEiAFG7GuW
    tiHU7hg4xjZ/6nYYsJZPmFD52Zi8NptfjsL6Ln3aHqlnh5Um6Bv1gQ==';
            $sign = 'cc8a7367aa99e17d61e7c6076ca91bdd';
            $sign_key = '9e9581eb2f8c4c002f88405cd1444327';
            $my_sign = ApiCommon::GetApiSign($rand_str, $time, $token, $data, $sign_key);

            print_r("<pre>");
            print_r($my_sign);
            print_r("<br>");
            print_r($sign);

            exit;
            $cont = 0;
            for ($i = 0; $i < 10000; $i++) {
                $a = \Yii::$app->cache->get('my_api_key_981935e849c9d3180f83675efb0083c96dbac086');
                if (!isset($a) || empty($a)) {
                    $cont++;
                }
            }

            print_r($cont);

            exit;

            $a = ClientGoodsUtil::GetGoldGoodsList();
            //$a = doubleval(0.01)*100;
            print_r($a);


            //data字符串无法解析
            //WiAmQ8FX@vyBBuI%%B)S4g--1MKmb9t7
            //6jY3Ni0zE`-`R%JwBUJKn2lr2RrClMEC
            //inbHq#$$Uppo*5Y8zw$gXx4|X6V%VPI5
            //5xb3VNwHlC903C$HaMBHai6zRjf!G7Ot
            //3qZ1@owwmunB!q)lCo1~iKjiTTQNA3#c
            //&_gc)MCV*o9h=neJknS1cSrdMSm4C3mV
            //token令牌错误
            //my_api_key_642baf7271be2b7c956e9c40c64bbe9f51c0d64b
            //8d1a1fac2008f5207c9394eaf6736a5fff576f92


            exit;
            $a = ClientUtil::SearchUser('1', 1, 5, 25);
            print_r($a);

            exit;
            $params = [
                'client_no' => 212153,
                'nick_name' => '你问大神',
                'manage_id' => 1,
                'manage_name' => '带我',
                'operate_type' => '2',
                'create_time' => date('Y-m-d H:i:s')
            ];
            //增加禁用日志
            if (!ClientUtil::CreateCloseUserByLog($params, $error)) {
                print_r($error);
                exit;
            }
            print_r('ok');

            EXIT;

            //$time = '';
            $time = \Yii::$app->cache->get('send_people_im_time');

            $end_time = time();

            $rst = $end_time - $time;
            print_r($rst);

            exit;
            $star_time = time();

            \Yii::$app->cache->set('send_people_im_time', $star_time);
            print_r($star_time); //1477884410

            exit;
            $red_num = 11;
            $red_money = 10;
            $dataAry = UsualFunForStringHelper::GenRandRePacketsData($red_num, $red_money, $index_max, $error);
            if (!$dataAry) {
                print_r($error);
                exit;
            }
            print_r("<pre>");

            print_r($dataAry);
            print_r("<br >");
            print_r($index_max);
            print_r("<br >");
            print_r(array_sum($dataAry));

            exit;
            \Yii::$app->cache->delete('user_menu_1');

            exit;
            $card = [2, 12, 13, 4, 7];
            $a = NiuNiuGameHelper::JudgeCowCow($card);
            print_r($a);
            exit;
            $a = 24 % 10;
            print_r($a);
            exit;
            $banker_num = 9;
            $poker_num = 0;
            $i = 0;
            if (($banker_num == 0) || ($banker_num >= $poker_num && $poker_num != 0)) {
                $i++;
                $rst = '-1';
            } else {
                $i--;
                $rst = '1';
            }
            print_r('i=' . $i);
            print_r("<br >");
            print_r('rst=' . $rst);

            exit;
            $a = NiuNiuGameUtil::GetNiuNiuGameInfo(321);
            //$a = LivingHotUtil::GetLivingAudienceFromContribution(10,5);
            //$a = ChatFriendsUtil::GetContributionBoard(108039,1,50,25);
            print_r('<pre>');
            print_r($a);

            exit;
            //充值游戏币
             if (!GoldsAccountUtil::UpdateGoldsAccountToAdd('1424', 415, 2, 4, 1000000, $error)) {
                 echo 'no';
                 exit;
             }
                echo 'OK';
            exit;

            $data['data'] = [
                'is_win' => 1,
                'win_money_num' => 100,
                'seat_num' => 1,
            ];
            $info = '{"game_id":2630,"poker_info":{"1":{"user_id":"566","pic":"http:\\/\\/oss-cn-hangzhou.aliyuncs.com\\/mblive-demo\\/client-pic\\/ocimg_580df8ffd64d6.jpg","nick_name":"\\u529b\\u6c14\\u6bd4\\u4f60\\u5927","seat_status":"2","is_banker":2,"is_robot":"1","is_living_master":"2","is_win":2,"seat_num":"1","chip_num":100,"base_num":"100","multiple":"1","poker_result":"6","poker1":{"poker":9,"shape":4},"poker2":{"poker":6,"shape":1},"poker3":{"poker":6,"shape":4},"poker4":{"poker":8,"shape":4},"poker5":{"poker":7,"shape":2},"win_num":"1"},"2":{"user_id":"666","pic":"http:\\/\\/oss-cn-hangzhou.aliyuncs.com\\/mblive-demo\\/client-pic\\/ocimg_580481cc475c0.jpg","nick_name":"\\u8346\\u9759\\u5bcc","seat_status":2,"is_banker":1,"is_robot":2,"is_living_master":"1","is_win":1,"seat_num":"2","chip_num":-100,"base_num":100,"multiple":1,"poker_result":"4","poker1":{"poker":5,"shape":2},"poker2":{"poker":12,"shape":1},"poker3":{"poker":13,"shape":4},"poker4":{"poker":9,"shape":2},"poker5":{"poker":10,"shape":1},"win_num":"-1"},"3":{"user_id":"667","pic":"http:\\/\\/oss-cn-hangzhou.aliyuncs.com\\/mblive-demo\\/client-pic\\/ocimg_580482932b032.jpg","nick_name":"\\u7fc1\\u6d01\\u5c9a","seat_status":2,"is_banker":1,"is_robot":2,"is_living_master":"1","is_win":1,"seat_num":"3","chip_num":-100,"base_num":100,"multiple":1,"poker_result":"4","poker1":{"poker":12,"shape":3},"poker2":{"poker":10,"shape":2},"poker3":{"poker":12,"shape":4},"poker4":{"poker":4,"shape":1},"poker5":{"poker":10,"shape":3},"win_num":"-1"},"4":{"user_id":"668","pic":"http:\\/\\/oss-cn-hangzhou.aliyuncs.com\\/mblive-demo\\/client-pic\\/ocimg_58048293674b0.jpg","nick_name":"\\u5b87\\u6587\\u71d5","seat_status":2,"is_banker":1,"is_robot":2,"is_living_master":"1","is_win":2,"seat_num":"4","chip_num":100,"base_num":100,"multiple":1,"poker_result":"8","poker1":{"poker":3,"shape":4},"poker2":{"poker":7,"shape":4},"poker3":{"poker":8,"shape":2},"poker4":{"poker":11,"shape":3},"poker5":{"poker":11,"shape":4},"win_num":"1"}}}';
            //$info = '{"game_id":"95","game_status":"2","sync_time":"10","poker_info":{"1":{"user_id":"33","pic":"http:\/\/oss-cn-hangzhou.aliyuncs.com\/mblive-demo\/client-pic\/ocimg_57a98ea16521a.jpg","nick_name":"哇哈哈啊","seat_status":"2","is_banker":"1","is_robot":"1","is_living_master":"2","is_win":0,"chip_num":"0","base_num":"0","multiple":"0","poker_result":"-1","poker1":{"poker":9,"shape":1},"poker2":{"poker":2,"shape":1},"poker3":{"poker":7,"shape":3},"poker4":{"poker":8,"shape":2},"poker5":{"poker":6,"shape":4}},"2":{"user_id":"","pic":null,"nick_name":"","seat_status":"1","is_banker":"1","is_robot":"1","is_living_master":"1","is_win":0,"chip_num":"0","base_num":"0","multiple":"0","poker_result":"-1","poker1":{"poker":8,"shape":3},"poker2":{"poker":7,"shape":4},"poker3":{"poker":10,"shape":3},"poker4":{"poker":8,"shape":4},"poker5":{"poker":7,"shape":1}},"3":{"user_id":"","pic":null,"nick_name":"","seat_status":"1","is_banker":"1","is_robot":"1","is_living_master":"1","is_win":0,"chip_num":"0","base_num":"0","multiple":"0","poker_result":"-1","poker1":{"poker":2,"shape":4},"poker2":{"poker":3,"shape":1},"poker3":{"poker":11,"shape":4},"poker4":{"poker":4,"shape":2},"poker5":{"poker":13,"shape":2}},"4":{"user_id":"","pic":null,"nick_name":"","seat_status":"1","is_banker":"1","is_robot":"1","is_living_master":"1","is_win":0,"chip_num":"0","base_num":"0","multiple":"0","poker_result":"4","poker1":{"poker":8,"shape":1},"poker2":{"poker":3,"shape":3},"poker3":{"poker":10,"shape":2},"poker4":{"poker":7,"shape":2},"poker5":{"poker":6,"shape":1}}}}';
            //$data = '{"game_id":"570","game_status":"1","sync_time":"10","poker_info":{"1":{"user_id":"33","pic":"http:\/\/oss-cn-hangzhou.aliyuncs.com\/mblive-demo\/client-pic\/ocimg_57a98ea16521a.jpg","nick_name":"哇哈哈啊","seat_status":"2","is_banker":"1","is_robot":"1","is_living_master":"2","is_win":1,"chip_num":"0","base_num":"0","multiple":"0","poker_result":"1","poker1":{"poker":10,"shape":3},"poker2":{"poker":11,"shape":3},"poker3":{"poker":1,"shape":4},"poker4":{"poker":9,"shape":1},"poker5":{"poker":1,"shape":3}},"2":{"user_id":"","pic":null,"nick_name":"","seat_status":"1","is_banker":"1","is_robot":"1","is_living_master":"1","is_win":1,"chip_num":"0","base_num":"0","multiple":"0","poker_result":"-1","poker1":{"poker":8,"shape":2},"poker2":{"poker":1,"shape":1},"poker3":{"poker":11,"shape":4},"poker4":{"poker":4,"shape":3},"poker5":{"poker":3,"shape":2}},"3":{"user_id":"","pic":null,"nick_name":"","seat_status":"1","is_banker":"1","is_robot":"1","is_living_master":"1","is_win":1,"chip_num":"0","base_num":"0","multiple":"0","poker_result":"-1","poker1":{"poker":10,"shape":4},"poker2":{"poker":9,"shape":2},"poker3":{"poker":6,"shape":2},"poker4":{"poker":13,"shape":1},"poker5":{"poker":7,"shape":4}},"4":{"user_id":"","pic":null,"nick_name":"","seat_status":"1","is_banker":"1","is_robot":"1","is_living_master":"1","is_win":1,"chip_num":"0","base_num":"0","multiple":"0","poker_result":"3","poker1":{"poker":12,"shape":3},"poker2":{"poker":3,"shape":4},"poker3":{"poker":11,"shape":2},"poker4":{"poker":6,"shape":1},"poker5":{"poker":4,"shape":2}}}}';
            $rst = json_decode($info, true);
            $ppp = json_encode($data);
            print_r("<pre>");
            //var_dump($ppp);
            print_r($rst);
            exit;
            $a = \Yii::$app->cache->get('niuniu_game_info_1');
            $bb = json_decode($a, true);
            print_r("<pre>");
            print_r($bb);

            exit;
            /*$seat_cache = \Yii::$app->cache->delete('mb_api_game_grab_seats_1');
            exit;
            $aa =\Yii::$app->cache->get('niuniu_game_1');
            print_r($aa);
            exit;*/
            $rebotsList = [
                [
                    'client_id' => '57',
                    'nick_name' => 'da啊啊啊我sdas',
                    'pic' => 'http://q.qlogo.cn/qqapp/1105405817/CC0E513C3DA8C30ACBFB33052DC9B387/100',
                    'sex' => '男',
                ],
                [
                    'client_id' => '55',
                    'nick_name' => '我很在稳啊吃的',
                    'pic' => 'http://img3.imgtn.bdimg.com/it/u=3126115126,3225955371&fm=21&gp=0.jpg',
                    'sex' => '男',
                ],
                [
                    'client_id' => '83',
                    'nick_name' => 'dasda阿打算s',
                    'pic' => 'http://img3.imgtn.bdimg.com/it/u=3126115126,3225955371&fm=21&gp=0.jpg',
                    'sex' => '女',
                ],

                [
                    'client_id' => '65',
                    'nick_name' => '王抢有才手打',
                    'pic' => 'http://q.qlogo.cn/qqapp/1105405817/CC0E513C3DA8C30ACBFB33052DC9B387/100',//小图标即可
                    'sex' => '男'
                ],
                [
                    'client_id' => '88',
                    'nick_name' => '查了强强耳温枪',
                    'pic' => 'http://mbpic.mblive.cn/client-pic/ocimg_5788b7d4b7116.jpg',//小图标即可
                    'sex' => '男'
                ],
                [
                    'client_id' => '98',
                    'nick_name' => '嘎嘎屋LOD安全',
                    'pic' => 'http://img3.imgtn.bdimg.com/it/u=3126115126,3225955371&fm=21&gp=0.jpg',
                    'sex' => '女',
                ],
            ];

            $rst = GameRebotsHelper::GenRebots($rebotsList, $error);
            if ($rst === false) {
                var_dump($error);
                exit;
            }
            var_dump('ok');
            exit;

            //原数据存储 28.3Mb  更新后 11.7Mb  json_encode 11.2Mb  serialize 11.2Mb
            //set_time_limit(0);

            $fun_id = '66';
            $unique_no = 'oVKOWs0eUDCbraTs6xfyS3aDhSA8';
            $deviceNo = '8EA3192B-0D15-425B-97FF-35E23B4844B3';
            $device_type = 2;
            $time = 0.12;
            $data = $fun_id . "\t" . time() . "\t" . $unique_no . "\t" . $deviceNo . "\t" . $device_type . "\t" . $time . "\t" . '10.32.121.3';
            //$data = '{"fun_id":"get_client_info","create_time":"2016-10-16 00:00:02","unique_no":"oVKOWs0eUDCbraTs6xfyS3aDhSA8","device_no":"8EA3192B-0D15-425B-97FF-35E23B4844B3","device_type":"2","remark1":"0.019","remark2":"frontend\\zhiboapi\\v2\\ZhiBoGetClientInfo","remark3":"117.150.67.89"}';
            $data = json_encode($data);
            print_r($data);
            $file = \Yii::$app->getBasePath() . '/runtime/logs/test.log';
            for ($i = 0; $i < 200000; $i++) {
                file_put_contents($file, $data . "\n", FILE_APPEND);
            }
            print_r($data);
    //        $info = Des3Crypt::des_decrypt($data);
    //        print_r('<br >');
    //        print_r($info);
            exit;




            if (!JobUtil::AddJob('testhlq', '', $error)) {
                print_r($error);
                exit;
            }

            if (!JobUtil::AddJob('testhlq', '', $error)) {
                print_r($error);
                exit;
            }
            if (!JobUtil::AddJob('testhlq', '', $error)) {
                print_r($error);
                exit;
            }
            if (!JobUtil::AddJob('testhlq', '', $error)) {
                print_r($error);
                exit;
            }

            print_r('ok');
            exit;




            \Yii::$app->cache->delete('up_game_info_4390');
            $a = \Yii::$app->cache->get('up_game_info_4390');
            print_r($a);
            exit;
            $a = NiuNiuGameUtil::GetNiuNiuGameByLivingId(1, 1);
            print_r('<pre>');
            print_r($a);

            exit;
            $a = 0;
            if (empty($a)) {
                print_r('no');
                exit;
            }
            print_r('pok');

            exit;

            $game_id = 1;
            $poker_info = NiuNiuGameUtil::GetNiuNiuGameInfo($game_id);

            $data = [
                'game_id' => 1,
                'game_status' => 1,
                'sync_time' => 8,
            ];
            foreach ($poker_info as $info) {
                $poker = json_decode($info['poker_info'], true);
                $data['poker_info'][$info['seat_num']] = [
                    'user_id' => $info['user_id'],
                    'pic' => $info['pic'],
                    'nick_name' => $info['nick_name'],
                    'seat_status' => $info['seat_status'],
                    'is_banker' => $info['is_banker'],
                    'is_robot' => $info['is_robot'],
                    'is_living_master' => $info['is_living_master'],
                    'base_num' => $info['base_num'],
                    'multiple' => $info['multiple'],
                    'poker_result' => $info['poker_result'],
                    'poker1' => $poker['poker1'],
                    'poker2' => $poker['poker2'],
                    'poker3' => $poker['poker3'],
                    'poker4' => $poker['poker4'],
                    'poker5' => $poker['poker5'],
                ];

            }
            print_r("<pre>");
            print_r($poker_info);
            print_r($data);
            exit;

            if (!GoldsAccountUtil::UpdateGoldsAccountToAdd(1048, 33, 1, 1, 9000, $error)) {
                print_r($error);
                exit;
            }
            print_r('ok');
            exit;


            exit;
            $a = GoldsAccountUtil::GetGoldsAccountModleByUserId(3);
            print_r('<pre>');
            print_r($a->gold_account_id);

            exit;


            if (!GoldsAccountUtil::CreateAnGoldsAccountForNewRegisUser(3)) {
                return false;
            }
            exit;
            $num = UsualFunForStringHelper::CreateGUID();
            var_dump($num);
            exit;

            $rst = GameRebotsHelper::GetRebots(5);
            print_r('<pre>');
            print_r($rst);
            exit;
            $all = NiuNiuGameGrabSeatUtil::GetGameSeatInfo(1);

            print_r("<pre>");
            print_r($all);
            exit;
            //创建游戏记录
            $data['data'] = [
                'game_id' => UsualFunForStringHelper::CreateGUID(),
                'living_id' => 1,
                'unique_no' => 18223663852,
            ];

            $a = new ZhiBoStartNiuNiuGame();
            if (!$a->excute_action($data, $rst, $error)) {
                print_r($error);
                exit;
            }

            print_r("<pre>");
            print_r('OK');
            print_r($rst);
            exit;
            $data = [
                'aaa' => 11,
                'aaass' => 22,
            ];
            $info = [
                'bbb' => 22,
                'aaa' => 133,
            ];
            $arr[] = $data;
            $arr[] = $info;
            print_r('<pre>');
            print_r($arr);


            exit;

            $all = NiuNiuGameGrabSeatUtil::GetGameSeatInfo($s);
            print_r('<pre>');
            print_r($s);
            print_r($all);
            if (!empty($all)) {
                print_r('no empty');
                exit;
            }

            print_r('empty');
            exit;
            $arr = NiuNiuGameUtil::GetNiuNiuGamePokerInfo();
            foreach ($arr as $info) {
                $data[] = json_encode(['poker1' => $info[0], 'poker2' => $info[1], 'poker3' => $info[2], 'poker4' => $info[3], 'poker5' => $info[4]]);
            }

            print_r("<pre>");
            print_r($arr);
            print_r($data);

            exit;
            shuffle($arr);
            shuffle($arr);
            $poker = array_slice($arr, 0, 20);
            $poker_info = array_chunk($poker, 5);

            $rst = [];
            foreach ($poker_info as $info) {
                $card = [];
                foreach ($info as $i) {
                    $card[] = $i['poker'];
                }
                print_r("<pre>");
                print_r($card);
                $rst[] = NiuNiuGameHelper::JudgeCowCow($card);
            }

            print_r("<pre>");
            print_r($poker_info);
            print_r($rst);

            exit;
            $a = [8, 5, 13, 5, 12];
            $b = NiuNiuGameHelper::JudgeCowCow($a);
            print_r($b);

            exit;
            $a = [
                'poker1' => ['shape' => 1, 'poker' => 1],
                'poker2' => ['shape' => 2, 'poker' => 2],
                'poker3' => ['shape' => 2, 'poker' => 7],
                'poker4' => ['shape' => 2, 'poker' => 2],
                'poker5' => ['shape' => 2, 'poker' => 8],

            ];

            //$b = '{"poker1":{"shape":1,"poker":1},"poker2":{"shape":2,"poker":2},"poker3":{"shape":2,"poker":2},"poker4":{"shape":2,"poker":2},"poker5":{"shape":2,"poker":2}}';
            $data = json_encode($a);
            $b = $data;
            $rst = json_decode($b, true);
            print_r("<pre>");
            print_r($data);
            print_r("<br />");
            print_r($rst);
            exit;
            $a = DynamicUtil::GetUserByDynamicInfo(45);
            print_r($a);

            exit;
            $dataAry = ['api_version' => 'v2'];
            $funData = require(\Yii::$app->getBasePath() . '/zhiboapi/ConfigV2.php');
            $actionName = 'get_peakdata';
            $class = 'frontend\zhiboapi\v2\ZhiBoGetPeakData';
            $actionClass = 'frontend\zhiboapi\\' . $dataAry['api_version'] . '\\' . $funData[$actionName];
            if (!class_exists($actionClass)) {
                print_r('找不到对应功能类' . $funData[$actionName]);
                exit;
            }
            print_r('ok');


            exit;
            $a = UsualFunForStringHelper::GetHHMMBySeconds(50527);
            print_r($a);
            exit;
            if (!LivingUtil::LivingTimeStatistics(47493, $out, $error)) {
                print_r($error);
                exit;
            }
            print_r($out);
            exit;
            $data = [
                'key_word' => 'set_tencent_im',
                'user_id' => '-3',
                'nick_name' => '群系统通知消息',
                'pic' => '',
            ];
            //注册腾讯用户
            if (!JobUtil::AddImJob('tencent_im', $data, $error)) {
                \Yii::getLogger()->log('im job save error :' . $error, Logger::LEVEL_ERROR);
                exit;
            }
            echo 'ok';
            exit;
            $update = 'update mb_friends_circle set check_num = check_num + 1 WHERE dynamic_id = :md';
            $query = \Yii::$app->db->createCommand($update, [
                ':md' => 303
            ])->execute();

            if ($query <= 0) {
                $error = '更新红包动态打赏次数失败';
                \Yii::getLogger()->log($error . ' :' . \Yii::$app->db->createCommand($update, [
                        ':md' => 303
                    ])->rawSql, Logger::LEVEL_ERROR);
                print_r($error);
                exit;
            }
            echo 'ok';
            exit;
            $data['user_id'] = 533;
            $new_dynamic = DynamicUtil::GetNewDynamicListInfo($data, 1, 100);
            foreach ($new_dynamic as $list) {
                $s = array_search($list, $new_dynamic);
                $list['is_click'] = '0';
                $click_info = \Yii::$app->cache->get('get_dynamic_like_' . $list['user_id'] . '_' . $list['dynamic_id'] . '_' . $LoginInfo['user_id']);
                if ($click_info !== false) {
                    $list['is_click'] = '1';
                }
                $new_dynamic[$s] = $list;
            }
            print_r('<pre>');
            print_r($new_dynamic);
            exit;
            $a = DynamicUtil::GetDynamicListInfo(446, 1, 10);
            print_r('<pre>');
            print_r($a);

            exit;
            $a = ChatFriendsUtil::GetAttentions(527, 1, 10, 527);
            print_r('<pre>');
            print_r($a);
            exit;

            $a = ActivityUtil::GetWinningInfo(8, 70);
            print_r($a);

            exit;

            $prize_info = ActivityUtil::GetActivityPrizeInfo(8, true);
            $a['prize_info'] = $prize_info;
            //$a = json_encode($a);
            print_r('<pre>');
            print_r($a);

            exit;
            $rst = ActivityUtil::GetActivityPrizeInfo(3);
            print_r('<pre>');
            print_r($rst);

            exit;
    //测试个人信息获取接口
            $data['data'] = [
                'user_id' => '1',
                'fields' => [
                ],
            ];

            $fields = [
                'user_id' => 'client_id as user_id',
                'nick_name' => 'nick_name',
                'client_no' => 'client_no',
                'alipay_no' => 'alipay_no',
                'pic' => 'IFNULL(nullif(main_pic,\'\'),bc.pic) as pic',
                'level_id' => 'level_id',
                'level_pic' => 'ls.level_pic',
                'sex' => 'sex',
                'color' => 'color',
                'level_bg' => 'level_bg',
                'font_size' => 'font_size',
                'age' => 'age',
                'city' => 'bc.city',
                'sign_name' => 'sign_name',
                'send_ticket_count' => 'send_ticket_count',
                'attention_num' => 'attention_num',
                'funs_num' => 'funs_num',
                'ticket_count_sum' => 'ticket_count_sum',
                'ticket_count' => 'ticket_count',
                'ticket_real_sum' => 'ticket_real_sum',
                'today_ticket_num' => 'IFNULL(real_ticket_num,0) as today_ticket_num',
                'bean_balance' => 'bean_balance',
                'virtual_bean_balance' => 'virtual_bean_balance',
                'cash_rite' => 'cash_rite',
                'is_bind_weixin' => 'is_bind_weixin',
                'is_bind_alipay' => 'is_bind_alipay',
                'is_contract' => 'is_contract',
                'real_validate' => 'is_centification as real_validate',
                'is_attention' => 'is_attention',
                'is_black' => 'is_black',
                'is_live' => 'IFNULL(ll.status,1) as is_live',
                'first_reward' => 'first_reward',
                'living_id' => 'll.living_id',
                'group_id' => 'IFNULL(group_id, \'\') as group_id',
                'is_join' => 'is_join',
                'tx_group_id' => 'IFNULL(tx_group_id, \'\') as tx_group_id',
                'group_name' => 'IFNULL(group_name, \'\') as group_name',
                'group_pic' => 'IFNULL(fg.pic, \'\') as group_pic',
                'advance_notice' => 'IFNULL(advance_notice, \'\') as advance_notice',
            ];
            $userFinish = 2;
            $filesInput = $data['data']['fields'];
            $files = $filesInput;
            if (!is_array($files)) {
                $files = [];
            }
            if (!is_array($filesInput)) {
                $filesInput = [];
            }
            $joint = [      //额外获取拼接的字段
                'is_attention',
                'is_black',
                'today_ticket_num',
                'cash_rite',
                'first_reward',
                'is_join',
            ];
            $back = [];
            foreach ($joint as $un) {
                if (in_array($un, $filesInput)) {
                    $back[] = $un;
                }
            }

            $client_info = [ //必须检测的字段
                'user_id',
                'nick_name',
                'pic',
                'sex',
            ];

            if (!empty($filesInput)) {
                foreach ($client_info as $c) {
                    if (!in_array($c, $filesInput)) {
                        $filesInput[] = $c;
                    }
                }
            }

            $filedRst = [];
            if (!empty($filesInput))   //获取要查询的字段
            {
                foreach ($filesInput as $field) {
                    if (!isset($fields[$field])) {
                        $error = '请求的字段不存在';
                        echo $error;
                        exit;
                    }
                    $filedRst[] = $fields[$field];
                }
            } else {
                foreach ($fields as $field) {
                    $filedRst[] = $field;
                }
                $back = $joint;
            }

            $unset = [          //需要删除的字段
                'is_attention',
                'is_black',
                'first_reward',
                'is_join',
            ];
            foreach ($unset as $t)   //删除不需要的字段
            {
                $set_false = array_search($t, $filedRst);
                if ($set_false !== false) {
                    unset($filedRst[$set_false]);
                }
            }
            $to_user = 8;
            if (!empty($data['data']['user_id']))    //如果是查询多个,将id组合
            {
                $to_user = $data['data']['user_id'];
            }
            $self_user_id = 8;
            $client_type = $data['data']['client_type'];
            if (!ClientInfoUtil::GetUserData($filedRst, $to_user, $self_user_id, $back, $userInfo, $client_type, $error)) {
                echo $error;
                exit;
            }
            if (empty($in['nick_name']) || empty($in['pic']) || empty($in['sex'])) {
                $userFinish = 1;
            }
            $userInfo['user_finish'] = $userFinish;
            if (empty($userInfo['first_reward'])) {
                unset($userInfo['first_reward']);
            }
            if (!empty($files)) {
                foreach ($client_info as $c) {
                    if (!in_array($c, $files)) {
                        unset($userInfo[$c]);
                    }
                }
            }

            print_r("<pre>");
            print_r($userInfo);
            exit;



            exit;



            $dynamic_like = \Yii::$app->cache->get('get_dynamic_like_527_199_522');
            if ($dynamic_like !== false) {
                print_r('ok');
                exit;
            }
            print_r($dynamic_like);
            exit;



            exit;

            $a = DynamicUtil::IsRewardByDynamic(26, 3);
            print_r('<pre>');
            print_r($a);
            exit;

            $data['user_id'] = 3;
            $a = DynamicUtil::GetNewDynamicListInfo($data, 1, 10);
            print_r("<pre>");
            print_r($a);
            exit;
            $Dynamic_test = DynamicUtil::GetDynamicListInfo(523, 1, 15);
            $Dynamic_list = [];
            //$len = count($Dynamic_test);

            foreach ($Dynamic_test as $list) {
                $list['is_click'] = 0;
                $click_info = \Yii::$app->cache->get('get_dynamic_like_' . $list['user_id'] . '_' . $list['dynamic_id'] . '_2');
                if ($click_info !== false) {
                    $list['is_click'] = 1;
                }
                $time = date('Y-m', strtotime($list['create_time']));
                $Dynamic_list[$time][] = $list;

            }
            /*$test = [];
            foreach($Dynamic_list as $key => $v)
            {
                $test[] = [
                    'date'=>$key,
                    'list'=>$Dynamic_list[$key]
                ];
            }*/

            //$date = array_keys($Dynamic_list);
            $data = [
                'list' => $Dynamic_list,
            ];
            $a = json_encode($data);
            print_r("<pre>");
            print_r($a);
            //print_r($date);
            exit;
            //测试多人信息获取接口
            $data['data'] = [
                'user_id' => [1, 2, 3, 4, 5, 6, 7, 8],
                'fields' => [

                ],
            ];

            $fields = [
                'user_id' => 'client_id as user_id',
                'nick_name' => 'nick_name',
                'client_no' => 'client_no',
                'alipay_no' => 'alipay_no',
                'pic' => 'pic',
                'level_id' => 'level_id',
                'level_pic' => 'ls.level_pic',
                'sex' => 'sex',
                'color' => 'color',
                'level_bg' => 'level_bg',
                'font_size' => 'font_size',
                'age' => 'age',
                'city' => 'bc.city',
                'sign_name' => 'sign_name',
                'send_ticket_count' => 'send_ticket_count',
                'attention_num' => 'attention_num',
                'funs_num' => 'funs_num',
                'ticket_count_sum' => 'ticket_count_sum',
                'ticket_count' => 'ticket_count',
                'ticket_real_sum' => 'ticket_real_sum',
                'today_ticket_num' => 'IFNULL(real_ticket_num,0) as today_ticket_num',
                'bean_balance' => 'bean_balance',
                'virtual_bean_balance' => 'virtual_bean_balance',
                'cash_rite' => 'cash_rite',
                'is_bind_weixin' => 'is_bind_weixin',
                'is_bind_alipay' => 'is_bind_alipay',
                'is_contract' => 'is_contract',
                'real_validate' => 'is_centification as real_validate',
                'is_attention' => 'is_attention',
                'is_black' => 'is_black',
                'first_reward' => 'first_reward',
            ];

            $filesInput = $data['data']['fields'];
            $files = $filesInput;
            if (!is_array($files)) {
                $files = [];
            }
            if (!is_array($filesInput)) {
                $filesInput = [];
            }
            $joint = [      //额外获取拼接的字段
                'is_attention',
                'is_black',
                'today_ticket_num',
                'cash_rite',
                'first_reward',
            ];
            $back = [];
            foreach ($joint as $un) {
                if (in_array($un, $filesInput)) {
                    $back[] = $un;
                }
            }
            $client_info = [ //必须检测的字段
                'user_id',
                'nick_name',
                'pic',
                'sex',
            ];

            if (!empty($filesInput)) {
                foreach ($client_info as $c) {
                    if (!in_array($c, $filesInput)) {
                        $filesInput[] = $c;
                    }
                }
            }

            $filedRst = [];
            if (!empty($filesInput))   //获取要查询的字段
            {
                foreach ($filesInput as $field) {
                    if (!isset($fields[$field])) {
                        $error = '请求的字段不存在';
                        echo $error;
                        exit;
                    }
                    $filedRst[] = $fields[$field];
                }
            } else {
                foreach ($fields as $field) {
                    $filedRst[] = $field;
                }
                $back = $joint;
            }

            $unset = [          //需要删除的字段
                'is_attention',
                'is_black',
                'first_reward'
            ];
            foreach ($unset as $t)   //删除不需要的字段
            {
                $set_false = array_search($t, $filedRst);
                if ($set_false !== false) {
                    unset($filedRst[$set_false]);
                }
            }
            $info_user = $data['data']['user_id'];
            $to_user = 8;
            $user_list = '';
            if (!empty($info_user))    //如果是查询多个,将id组合
            {
                if (is_array($info_user)) {
                    $len = count($info_user);
                    $i = 0;
                    foreach ($info_user as $un) {
                        $i++;
                        $user_list .= intval($un);
                        if ($i != $len) {
                            $user_list .= ',';
                        }
                    }
                }
                $to_user = $user_list;
            }
            $self_user_id = 8;
            if (!ClientInfoUtil::GetUserDataParams($filedRst, $to_user, $self_user_id, $back, $userInfo, $error)) {
                echo $error;
                exit;
            }
            foreach ($userInfo as $in) {
                $s = array_search($in, $userInfo);
                $in['userFinish'] = 2;
                if (empty($in['nick_name']) || empty($in['pic']) || empty($in['sex'])) {
                    $in['userFinish'] = 1;
                }
                $userInfo[$s] = $in;
            }
            foreach ($userInfo as $t) {
                $s = array_search($t, $userInfo);
                if (empty($t['first_reward'])) {
                    unset($t['first_reward']);
                }
                $userInfo[$s] = $t;
            }
            if (!empty($files)) {
                foreach ($client_info as $c) {
                    foreach ($userInfo as $us) {
                        $s = array_search($us, $userInfo);
                        if (!in_array($c, $files)) {
                            unset($us[$c]);
                        }
                        $userInfo[$s] = $us;
                    }
                }
            }

            print_r("<pre>");
            print_r($userInfo);
            exit;


            if (isset($a)) {
                echo "no";
                exit;
            }
            echo 'ok';
            exit;




            $a = \Yii::$app->cache->get('mb_api_login_18986699954');
            $a = unserialize($a);
            print_r($a);

            exit;
            $fileName = './api_logs/api_log_' . date('Y-m-d') . '.txt';
            /*$my_file = fopen($fileName,'a');
            $int = fwrite($my_file,$data."\n");*/
            $int = file_put_contents($fileName, $data . "\n", FILE_APPEND);
            if (!$int) {
                echo 'api文件内容写入失败';
                exit;
            }
            //fclose($my_file);

            exit;
            /*$a = 'aaa';
            $b = ['aaa'=>'sss',''];
            if(in_array($a,$b))
            {
                echo 'ok';
                exit;
            }
            echo "NO";

            exit;*/



            $key = 'enter_room_no_sub_person_159';
            $rst = \Yii::$app->cache->delete($key);


           // $rst = \Yii::$app->cache->get($key);
            print_r($rst);


            exit;
            $cnt = \Yii::$app->cache->get('carousels_info');
            $cnt = unserialize($cnt);
            print_r($cnt);

            exit;

            $rst = WxPayOrderQueryApp::CheckOrderAppResult('ZHF-RG-16-07-110015', $out);
            print_r('<pre>');
            print_r($rst);
            print_r($out);
            exit;
            var_dump(0 == '');

            exit;
            //12010  ~ 13077 ~ 21525 ~  4875 ~ 9955 ~ 4919 两次~ 4875 ~ 18196
            //  24     24      24       80     24         80     80        24
            //增加实际豆
            $user_id = 18196;
            $bean_num = 300; //980  300
            $zengsong = 24;  // 80   24
            $phone = \Yii::$app->params['service_tel'];
            $text_content = '亲爱的蜜播用户，由于网络延迟原因，您充值的' . $bean_num . '蜜豆和赠送' . $zengsong . '蜜豆现已到账，请到“我的豆”页面核实，若还有问题请加官方客服QQ群' . $phone . '咨询哦~';
            if (!BalanceUtil::AddReadBeanNum($user_id, $zengsong, $error)) {
                var_dump($error);
                exit;
            }

            if (!TimRestApi::openim_send_Text_msg(strval($user_id), $text_content, $error)) {
                \Yii::getLogger()->log('发送腾讯云通知消息异常: ' . $error, Logger::LEVEL_ERROR);
                $error = '充值成功，发送腾讯云私信失败!';
                echo $error;
                exit;
            }
            echo 'ok';
            exit;


            TimRestApi::init();
            $sign = TimRestApi::generate_user_sig(strval(25));
            print_r($sign[0]);

            exit;
            //检查短信余额
            $url = 'http://120.26.69.248/msg/QueryBalance?account=4q9v4e&pswd=o5eF7fIO';
            $back = UsualFunForNetWorkHelper::HttpGet($url);
            echo $back;
            exit;



            $a = AlipayUtil::QueryOrderStatus('ZHF-RG-16-07-040014', '', $out);
            print_r('<pre>');
            print_r($a);
            print_r($out);

            exit;
            //$a = WxPayOrderQuery::CheckOrderResult('ZHF-RGWEB-16-07-040047',$out);
            $a = WxPayOrderQuery::CheckOrderResult('ZHF-RGWEB-16-07-040047', $out);
            print_r('<pre>');
            print_r($a);
            print_r($out);

            /*对应处理类:frontend\business\OtherPay\GetPayParamsKinds\GetWxWebpayParamsForRecharge
    2016-07-04 20:31:30 [101.226.125.14][-][-][error][application] web支付:array (
            'appId' => 'wx19f6ec4aec39c380',
            'nonceStr' => 'eh7tfkkv4ru9tbsqq0djfqydwxvaahdr',
            'package' => 'prepay_id=wx20160704203130ec827683110959737230',
            'signType' => 'MD5',
            'timeStamp' => '1467635490',
            'paySign' => 'EF5BBA794973628D57A112FB559E70FF',
        )*/
        }
} 